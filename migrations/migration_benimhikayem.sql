-- migration_benimhikayem.sql
-- Benim Hikayem (karar ağacı oyunu) ve Farkındalık Zinciri için DB altyapısı
-- Bu dosyadan ÖNCE mevcut tüm migration'lar çalıştırılmış olmalıdır.
USE db_genclik_rehberim;

-- 1) Yeni oyun türlerini activities ENUM'a ekle
ALTER TABLE activities
  MODIFY type ENUM(
    'bulmaca','eslestirme','kategori','wordle','cengel','bosluk',
    'benimhikayem','farkindalikzinciri'
  ) NOT NULL;

-- 2) Yeni aktiviteleri ekle (idempotent — FROM DUAL + WHERE NOT EXISTS MySQL uyumlu)
INSERT INTO activities (name, description, type, max_score, is_active)
SELECT 'Benim Hikayem',
       'Akran zorbalığı senaryolarında doğru kararlar vererek farkındalık kazan',
       'benimhikayem', 100, 1
FROM dual
WHERE NOT EXISTS (SELECT 1 FROM activities WHERE type = 'benimhikayem');

INSERT INTO activities (name, description, type, max_score, is_active)
SELECT 'Farkındalık Zinciri',
       'Bilgisayarın verdiği kelimeden zincir kurarak empati kelimelerini keşfet',
       'farkindalikzinciri', 100, 1
FROM dual
WHERE NOT EXISTS (SELECT 1 FROM activities WHERE type = 'farkindalikzinciri');

-- 3) Karar ağacı düğümleri (Benim Hikayem)
CREATE TABLE IF NOT EXISTS story_nodes (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  scenario  TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=Kantin, 2=Siber',
  text      TEXT         NOT NULL,
  type      ENUM('start','middle','end') NOT NULL DEFAULT 'middle',
  feedback  TEXT         DEFAULT NULL COMMENT 'Seçim sonrası gösterilecek pedagojik mesaj',
  PRIMARY KEY (id),
  KEY idx_sn_scenario_type (scenario, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) Karar ağacı seçenekleri (Benim Hikayem)
CREATE TABLE IF NOT EXISTS story_choices (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  node_id      INT UNSIGNED NOT NULL,
  choice_text  VARCHAR(255) NOT NULL,
  next_node_id INT UNSIGNED NOT NULL,
  points       TINYINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY fk_sc_node (node_id),
  CONSTRAINT fk_sc_node
    FOREIGN KEY (node_id) REFERENCES story_nodes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SENARYO 1: KANTİN ZORBALIGI (scenario=1)
-- ============================================================
-- Maksimum kazanılabilir puan: 50
-- Optimal yollar: node1→2 (50pt) | node1→4→7 (25+25=50pt)
-- ============================================================

INSERT INTO story_nodes (id, scenario, text, type, feedback) VALUES
(1,
 1,
 'Öğle arası kantinde sıra bekliyorsun. Tam sıranın geldiği anda iki öğrenci önüne geçerek "Sen bizim arkamızdaydın, git geri!" diye bağırıyor. Bu bir yalan; sen onlardan önce geldin. Çevrendeki diğer öğrenciler sizi izliyor.',
 'start',
 NULL
),
(2,
 1,
 'Öğretmeni buldun ve yaşananı anlattın. Öğretmen diğer öğrencilerle konuştu; sorun yerinde çözüldü ve sen özür aldın.',
 'end',
 'Harika bir karar! Bir yetişkinden yardım istemek hem cesur hem de en etkili adımdır. Zorbalık karşısında sessiz kalmak zorunda değilsin.'
),
(3,
 1,
 'Sessizce sıranın arkasına geçtin. İçinde çok haksızlık hissediyorsun ama ne yapacağını bilemiyorsun.',
 'middle',
 NULL
),
(4,
 1,
 'Sakin ama kararlı bir sesle "Hayır, ben öndeydim" dedin. Sesi duyulan, net bir itiraz. Zorba öğrenciler seni itmeden bıraktılar ama gülüşerek devam ettiler.',
 'middle',
 NULL
),
(5,
 1,
 'Arkadaşın seninle birlikte öğretmene gitti. Öğretmen her iki tarafı da dinledi ve adaletli bir çözüm buldu.',
 'end',
 'Mükemmel! Güvendiğin biriyle birlikte hareket etmek, zorbalığı durdurmada çok etkilidir. Destek istemek güçlülüktür.'
),
(6,
 1,
 'Hiçbir şey yapmadın; öfke ve üzüntüyle öğle yemeği yedin. Bu his gün boyu seni takip etti.',
 'end',
 'Bu duyguyu içinde taşımak çok ağır. Güvendiğin birine anlatmak hem seni hafifletir hem de zorbalığın tekrar etmesini önler.'
),
(7,
 1,
 'Sesini duyduğunda çevre öğrencilerden üçü "O haklı, biz de gördük" dedi. Zorba öğrenciler şaşırıp geri adım attı.',
 'end',
 'Cesur olmak başkalarını da harekete geçirebilir! Akranlarının desteğiyle zorbalığı yerinde durdurdun.'
),
(8,
 1,
 'Kantinden çıkıp koridorda bir öğretmen buldun ve durumu anlattın. Öğretmen hemen müdahale etti.',
 'end',
 'İyi karar! Yetişkinlerden yardım istemek her zaman doğru bir seçimdir. Bunu yapabilmek büyük cesaret gerektirir.'
),

-- ============================================================
-- SENARYO 2: SİBER ZORBALIK (scenario=2)
-- ============================================================
-- Maksimum kazanılabilir puan: 50
-- Optimal yollar: node9→10 (50pt) | node9→11→13 (0+45=45pt)
-- ============================================================

(9,
 2,
 'Akşam telefonuna baktığında bir sınıf arkadaşının sana sosyal medyadan kötü bir mesaj attığını ve bunu sınıf grubuna da paylaştığını gördün. Ekranda utandırıcı bir şey yazıyor; 20 kişi görmüş.',
 'start',
 NULL
),
(10,
 2,
 'Mesajın ekran görüntüsünü alıp bir ebeveynine ya da öğretmenine gösterdin. Büyükler durumu hemen ele aldı ve zorbalık sona erdi.',
 'end',
 'Mükemmel karar! Siber zorbalıkta kanıt toplamak ve büyüklere bildirmek en doğru adımdır. Kanıtın olduğunda yardım almak çok daha kolaydır.'
),
(11,
 2,
 'Sinirle karşılık yazdın. Tartışma büyüdü; başka sınıf arkadaşları da dahil oldu ve ortam daha da kötüleşti.',
 'middle',
 NULL
),
(12,
 2,
 'Kişiyi engelledi ve grubu kapattın. Anlık olarak mesajları görmekten kurtuldun, ama kanıt almadan bu işi yaptın.',
 'end',
 'Engelleme iyi bir başlangıç, ancak ekran görüntüsü almadan önce engellersen kanıtı kaybedebilirsin. Büyüklere bildirmeden zorunun çözülmesi daha güç olabilir.'
),
(13,
 2,
 'Dur, derin bir nefes aldın. Ekran görüntüsü alıp güvendiğin birine anlattın. Büyükler seni destekledi.',
 'end',
 'Çok doğru bir karar! İlk tepkinin aksine geri adım atabilmek büyük bir olgunluk işareti. Kanıt topladın ve yardım aldın.'
),
(14,
 2,
 'Telefonu kapattın; ama mesajlar aklından çıkmıyor, uyuyamıyorsun.',
 'middle',
 NULL
),
(15,
 2,
 'Güvendiğin kişi seni dinledi ve birlikte durumu çözmenize yardım etti.',
 'end',
 'Bravo! Duygularını paylaşmak hem seni rahatlatır hem de zorbalığın devam etmesini önler. Güvendiğin birini bulmak çok önemlidir.'
),
(16,
 2,
 'Telefonu kapattın ve kimseye söylemedin. Zorbalık günlerce devam etti.',
 'end',
 'Bu duyguyu yalnız taşımak çok ağır. Bir dahaki sefere bir yetişkine ya da güvendiğin birine anlatmayı dene. Yardım istemek zayıflık değil, aksine güçtür.'
);

-- Senaryo 1 Seçimleri
INSERT INTO story_choices (node_id, choice_text, next_node_id, points) VALUES
(1, 'Hemen bir öğretmeni bul ve durumu anlat',                        2, 50),
(1, 'Sessizce sıranın arkasına geç',                                   3, 10),
(1, 'Sakin ama kararlı bir sesle "Hayır, ben öndeydim" de',            4, 25),
(3, 'En yakın arkadaşına anlat; birlikte öğretmene gidin',             5, 35),
(3, 'Hiçbir şey yapma, öğle yemeğine geç',                            6,  0),
(4, 'Çevredeki öğrencilerden tanıklık etmelerini iste',                7, 25),
(4, 'Kantinden çık ve bir öğretmen bul',                               8, 20);

-- Senaryo 2 Seçimleri
INSERT INTO story_choices (node_id, choice_text, next_node_id, points) VALUES
(9,  'Mesajın ekran görüntüsünü al, bir ebeveyne ya da öğretmene göster', 10, 50),
(9,  'Sinirle karşılık yaz',                                               11,  0),
(9,  'Kişiyi engelle ama kimseye söyleme',                                 12, 20),
(11, 'Dur; ekran görüntüsü al ve güvendiğin birine anlat',                 13, 45),
(11, 'Telefonu kapat ve üzül',                                             14,  0),
(14, 'Güvendiğin birine (anne, baba veya öğretmen) anlat',                 15, 30),
(14, 'Telefonu kapat, kimseye söyleme',                                    16,  0);
