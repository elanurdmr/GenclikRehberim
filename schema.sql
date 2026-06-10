-- ============================================================
-- Gençlik Rehberim | Veritabanı Şeması (TEK YETKİLİ KAYNAK)
-- Akran Zorbalığı Farkındalık Projesi
-- ------------------------------------------------------------
-- Sıfırdan kurulum için yalnızca bu dosyayı çalıştırmanız yeterlidir.
-- migrations/ klasöründeki dosyalar yalnızca ESKİ kurulumları
-- güncellemek içindir; yeni kurulumda gerekmezler.
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_genclik_rehberim
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_turkish_ci;

USE db_genclik_rehberim;

-- ============================================================
-- Tablo 1: users — Admin ve Öğrenci kullanıcıları
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,                          -- bcrypt hash
    role        ENUM('admin','student') NOT NULL DEFAULT 'student',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 2: activities — Oyun/Etkinlik tanımları
-- type ENUM TÜM oyun türlerini içerir (migration sırası önemsiz).
-- max_score sütunu save_score.php tarafından doğrulama için kullanılır.
-- ============================================================
CREATE TABLE IF NOT EXISTS activities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    type        ENUM('bulmaca','eslestirme','kategori','wordle','cengel','bosluk','benimhikayem','farkindalikzinciri') NOT NULL,
    max_score   INT      NOT NULL DEFAULT 100,
    is_active   TINYINT(1) NOT NULL DEFAULT 1   -- 0 = iç puanlama tipi, kullanıcıya gösterilmez
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 3: scores — Kullanıcı puan kayıtları
-- ============================================================
CREATE TABLE IF NOT EXISTS scores (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    activity_id INT NOT NULL,
    score       INT NOT NULL DEFAULT 0,
    max_score   INT NOT NULL DEFAULT 100,
    played_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 4: crossword_bank — Çengel bulmaca soru bankası
-- ============================================================
CREATE TABLE IF NOT EXISTS crossword_bank (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    clue        TEXT        NOT NULL,
    answer      VARCHAR(64) NOT NULL,
    sort_order  INT         NOT NULL DEFAULT 0,
    active      TINYINT(1)  NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 5: crossword_word_scores — Çengel bulmaca kelime başına puan
-- ============================================================
CREATE TABLE IF NOT EXISTS crossword_word_scores (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT         NOT NULL,
    puzzle_seed    VARCHAR(32) NOT NULL,
    direction      ENUM('across','down') NOT NULL,
    clue_number    INT         NOT NULL,
    points_awarded INT         NOT NULL,
    awarded_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_puzzle_clue (user_id, puzzle_seed, direction, clue_number),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 6: user_badges — Kazanılan rozetler
-- ============================================================
CREATE TABLE IF NOT EXISTS user_badges (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    badge_name VARCHAR(100) NOT NULL,
    earned_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_badge (user_id, badge_name),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Başlangıç Verileri
-- ============================================================

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@genclikrehberim.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Etkinlik kayıtları — id sırası oyun JS yapılandırmasıyla uyumludur.
INSERT INTO activities (id, name, description, type, max_score, is_active) VALUES
(1, 'Zorba Davranışa Karşı Koyma Bulmacası', 'Zorbalıkla başa çıkma yöntemlerini bulmaca ile öğren', 'bulmaca',    100, 1),
(2, 'Doğru mu, Yanlış mı? Eşleştirme',        'Doğru ve yanlış davranışları eşleştir',                'eslestirme', 140, 1),
(3, 'Zorbalık mı, Değil mi? Kategori',         'Eslestirme Bölüm 2 — iç puanlama tipi',               'kategori',   170, 0),
(4, 'Wordle — 5 Harfli Kelime',                'Altı denemede kelimeyi bul, Türkçe harf desteğiyle',  'wordle',     100, 1),
(5, 'Çengel Bulmaca',                          'Kesişimli çengel bulmaca ve gizli kelime',               'cengel',     100, 1),
(6, 'Boşluk Doldurma',                         'Eşleştirme oyunu Bölüm 2 — iç puanlama tipi',                       'bosluk',           80, 0),
(7, 'Benim Hikayem',                           'Akran zorbalığı senaryolarında doğru kararlar vererek farkındalık kazan', 'benimhikayem',    100, 1),
(8, 'Farkındalık Zinciri',                     'Bilgisayarın verdiği kelimeden zincir kurarak empati kelimelerini keşfet', 'farkindalikzinciri', 100, 1);

-- ============================================================
-- Tablo 6: story_nodes — Benim Hikayem karar ağacı düğümleri
-- ============================================================
CREATE TABLE IF NOT EXISTS story_nodes (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  scenario     TINYINT UNSIGNED NOT NULL DEFAULT 1,
  text         TEXT         NOT NULL,
  type         ENUM('start','middle','end') NOT NULL DEFAULT 'middle',
  feedback     TEXT         DEFAULT NULL,
  bonus_points TINYINT      NOT NULL DEFAULT 0 COMMENT 'Bitiş düğümlerine verilen ek puan (negatif olabilir)',
  PRIMARY KEY (id),
  KEY idx_sn_scenario_type (scenario, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tablo 7: story_choices — Benim Hikayem seçenekleri
-- ============================================================
CREATE TABLE IF NOT EXISTS story_choices (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  node_id      INT UNSIGNED NOT NULL,
  choice_text  VARCHAR(255) NOT NULL,
  next_node_id INT UNSIGNED NOT NULL,
  points       TINYINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY fk_sc_node (node_id),
  CONSTRAINT fk_sc_node FOREIGN KEY (node_id) REFERENCES story_nodes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Çengel bulmaca soru bankası (akran zorbalığı; cevaplar tek kelime / bitişik)
INSERT INTO crossword_bank (clue, answer, sort_order) VALUES
('Zorbalık olunca ne istemeliyiz?', 'YARDIM', 1),
('Yaşadığımız olayı kime anlatırız?', 'YETİŞKİN', 2),
('Yardım bulamazsak ne yapmaya devam etmeliyiz?', 'ARAMAK', 3),
('Zorba karşısında nasıl durmalıyız?', 'DİK', 4),
('Korkunca sakinleşmek için ne alıp veririz?', 'NEFES', 5),
('"Başarabilirim" gibi ifadeler (bitişik yazın).', 'OLUMLUSÖZ', 6),
('Fiziksel zorbalıkta gideceğimiz güvenli yer (bitişik).', 'GÜVENLİYER', 7),
('Daha güvende olmak için tercih edilen ortam.', 'KALABALIK', 8),
('Korkuyu göstermemek zorbayı ne yapar?', 'UZAKLAŞTIRIR', 9),
('Sözel zorbalıkta bazen yapmamız gereken hareket.', 'UZAKLAŞIRIZ', 10),
('Zorbalığı yetişkine iletmek.', 'BİLDİRME', 11),
('Kendini karşıdakinin yerine koymak.', 'EMPATİ', 12),
('Arkadaşına destek olma tutumu.', 'DAYANIŞMA', 13),
('Zorbalığa karşı sınıfta oluşan olumlu hava.', 'SAYGI', 14),
('Güvendiğimiz bir yetişkin.', 'ÖĞRETMEN', 15);

-- Benim Hikayem — Hikaye 1: Okul Kantini ve Dijital Dedikodu (Genişletilmiş)
-- 15 karar düğümü + 3 bitiş | Max = 100 | Tüm B ≈ 53 | Yanlış yapan öğrenci makul puan alır
INSERT INTO story_nodes (id, scenario, text, type, feedback, bonus_points) VALUES
(1,1,'Öğle arasında kantin sırasındasın. Bu hafta sınıfa nakil olan Eren hemen arkanda duruyor. Tam sıraya girecekken yanındaki arkadaşın Burak yüksek sesle: "Eski okulunda çanta çalmış biri var sıramızda, dikkat edin!" diyor. Birkaç öğrenci gülüşüyor. Eren''in yüzü kıpkırmızıya dönüyor.','start',NULL,0),
(2,1,'Kantinde herkes yer aldı. Eren köşede yalnız bir masada oturuyor; yanında hiç kimse yok. Burak sana sesleniyor: "Hadi gel bizimle otur." Yan masaya bakıyorsun; Eren tabağına bile bakmadan yemeğini yemeye çalışıyor.','middle',NULL,0),
(3,1,'Öğleden sonra teneffüste koridorda bir kalabalık toplandı. Burak ve iki arkadaşı Eren''in sırt çantasını kapıp içindekileri yere döküyor. "Çalıntı bir şey var mı bak!" diye bağırıyorlar. Öğretmen koridorun diğer ucunda.','middle',NULL,0),
(4,1,'Son ders: öğretmen fen projesi için gruplar kurdu. Herkes birini seçti; Eren hâlâ gruba dahil değil, kenarda bekliyor. Öğretmen "Herkes bir gruba girsin" dedi ve sana bakıyor.','middle',NULL,0),
(5,1,'Okul çıkışında çantanı alıp kapıya yürürken Burak seni durdurdu. Alçak sesle: "Bugün Eren''i korumaya mı çalıştın? Dikkat et, o grupla değilsen yalnız kalırsın." Diğer arkadaşlar uzaktan sizi izliyor.','middle',NULL,0),
(6,1,'Eve gelince telefona bakıyorsun. Sınıf grubuna Burak''ın Eren''in kötü bir fotoğrafını atıp üzerine alaylı yazılar yazdığını görüyorsun. 24 kişi beğenmiş. Arkadaşın Selin sana "Sence komik mi?" diye mesaj attı.','middle',NULL,0),
(7,1,'Saat 23.00. Paylaşım yayılmaya devam ediyor; yorumlar giderek sertleşiyor. Telefonunda okul yönetimi uygulaması açık ve "Yetkiliye Bildir" butonu gözüne çarpıyor. Uyuyamıyorsun.','middle',NULL,0),
(8,1,'Ertesi sabah okula geldin. Eren de var ama gözleri şişmiş, kimseyle konuşmuyor. Koridorda Burak ve arkadaşları Eren''i görünce fısıldaşıp gülüyor. Eren adımlarını yavaşlattı, sanki geri dönmek istiyor.','middle',NULL,0),
(9,1,'Birinci ders bitti. Teneffüste koridora çıktığında Eren''in tuvalet kapısının yanında yerde oturduğunu görüyorsun; kitap tutuyor ama sayfayı çevirmeden bakıyor. Çevresinde kimse yok.','middle',NULL,0),
(10,1,'Öğleden önce rehber öğretmen Sevgi Hanım sınıfa girdi: "Bazı olaylardan haberdar oldum; bu davranışlar zorbalıktır ve okul bunu ciddiye alıyor." Burak sandalyesinde kıpırdıyor. Sevgi Hanım "Gören veya bilen arkadaşlar benimle konuşabilir" dedi.','middle',NULL,0),
(11,1,'Öğle arasında Selin sana koşarak geldi: "Eren bugün son kez okula geldiğini söylüyor, okul değiştireceğini söylüyor. Artık dayanamıyormuş." Bahçeye bakıyorsun; Eren tek başına bir bankta oturuyor. Derslere 10 dakika var.','middle',NULL,0),
(12,1,'Öğle arası kantindeysin. Selin''le birlikte sıraya girdiniz. Eren de kantinde; yine tek başına köşe masaya yürüyor. Gözlerinin altında mor halkalar var, dün olmayan yüz ifadesi bu: tamamen kapalı.','middle',NULL,0),
(13,1,'Öğle arası biterken Burak seni koridorda köşeye çekti: "Rehber öğretmene bir şey mi söyledin? Olaylar büyümesin istiyorsan sus. Şimdi karar ver — bizden misin değil misin?" Çantanı sıkıca tutuyorsun.','middle',NULL,0),
(14,1,'Dersten önce Sevgi Hanım seni kapıda bekliyordu: "Müdür bey aile toplantısı düzenleyecek. Sana birkaç soru sormak istiyorum; o günlerde neler yaşandığını bana anlatabilir misin?" Defterini açıp not almaya hazır.','middle',NULL,0),
(15,1,'Müdür odasının kapısı önünde Sevgi Hanım seni karşıladı: "İçeride Eren''in ailesi ve Burak''ın ailesi var. Tanıklığın bu toplantının seyrini belirleyecek. Hazır mısın?"','middle',NULL,0),
(16,1,'Tanıklığın toplantının seyrini değiştirdi. Okul disiplin kurulu harekete geçti; Burak ailesiyle birlikte Eren''den özür dilemek zorunda kaldı. Fotoğraf tüm platformlardan kaldırıldı. Eren okul değiştirmedi ve senin sayende sınıfa gerçek anlamda dahil oldu. Sevgi Hanım seni "Empatinin Gücü" panosunda tebrik etti.','end','Empati ve cesaret bir araya geldiğinde mucizeler yaratır. Her kararınla hem Eren''in hayatını hem de sınıfın iklimini değiştirdin. Bu dünyayı daha iyi bir yer yapıyorsun!',10),
(17,1,'Kısmi tanıklığın bir şeyler değiştirdi. Burak uyarıldı, paylaşım kaldırıldı. Eren zorluklarla da olsa okulda kalmaya karar verdi. Mükemmel olmak zorunda değilsin; harekete geçmeye çalışmak başlı başına değerlidir.','end','Doğruyu yapmak her zaman kolay değildir. Önemli olan harekete geçmeyi denemektir. Bir dahaki seferde biraz daha cesur olmayı dene — adımların küçük bile olsa fark yaratır.',0),
(18,1,'Suskunluğun bu sefer pahalıya patlıyor. Yeterli tanıklık olmadığı için toplantı sonuçsuz kaldı. Eren hafta sonu okul değiştirdi. O bankta yalnız oturan çocuğun yüzü uzun süre aklından çıkmayacak.','end','Bazen en büyük zorbalık, yapılan kötülüğe sessiz kalmaktır. Bir dahaki seferde sesini yükseltmeye cesaret edebilirsin. Artık ne yapman gerektiğini biliyorsun — bu farkındalık çok değerlidir.',0);

INSERT INTO story_choices (node_id, choice_text, next_node_id, points) VALUES
(1,'"Bu doğru değil, kanıtın var mı?" diyerek Eren''i savunmak',2,7),
(1,'Sessizce sıradan çıkmak, karışmamak',2,3),
(1,'Gülerek arkadaşlarına katılmak',2,-2),
(2,'"Hep beraber oturalım, Eren de gelsin" demek',3,6),
(2,'Burak''ın masasına gitmek ama Eren''e gülümseyerek göz kırpmak',3,4),
(2,'Hiçbir şey demeden Burak''ın yanına oturmak',3,-2),
(3,'Hemen öğretmene koşmak ve durumu anlatmak',4,7),
(3,'"Dur!" diye bağırıp öne çıkmak',4,5),
(3,'Uzaktan seyretmek, araya girmemek',4,-2),
(4,'"Eren bizimle gelsin" demek',5,6),
(4,'Öğretmene fısıldayarak Eren''in gruba alınmasını istemek',5,4),
(4,'Sessiz kalmak, öğretmen başka çözüm bulsun diye beklemek',5,-2),
(5,'"Doğru gördüğüm şeyleri yapıyorum, bu değişmez" demek',6,7),
(5,'Omuz silkip sükunetle yürümeye devam etmek',6,3),
(5,'"Tamam tamam, bir daha karışmam" deyip geçmek',6,-2),
(6,'Selin''e "Bu siber zorbalık, ekran görüntüsünü alıp bildirmeliyiz" demek',7,7),
(6,'"Yanlış ama ne yapabilirim ki" yazmak',7,3),
(6,'"Gerçekten çok komik :)" yazıp paylaşımı beğenmek',7,-2),
(7,'"Yetkiliye Bildir" butonuna basmak ve ekran görüntüsünü eklemek',8,6),
(7,'Burak''a özel mesaj atıp "Fazla ileri gidiyorsun, kaldır onu" demek',8,4),
(7,'Telefonu kapatıp uyumaya çalışmak',8,-1),
(8,'Eren''in yanına gitmek ve "Merhaba, nasılsın?" demek',9,5),
(8,'"Günaydın" deyip kapıyı tutmak, içeri birlikte girmek',9,3),
(8,'Fark etmeden sınıfa girmek',9,-1),
(9,'"Merhaba, yalnız mısın? Yanına oturabilir miyim?" demek',10,6),
(9,'Gülerek baş sallayıp geçmek',10,4),
(9,'Başka yöne bakmak',10,-1),
(10,'Dersten sonra Sevgi Hanım''ın odasına gidip her şeyi anlatmak',11,7),
(10,'Okulun anonim ihbar kutusuna not bırakmak',11,4),
(10,'"Herkes kendi bileceği iş" diyerek bir şey yapmamak',11,-2),
(11,'Hemen bahçeye gidip Eren''in yanına oturmak',12,6),
(11,'Selin ile birlikte gidip ikisi de destek vermek',12,4),
(11,'"Ben ne yapabilirim ki" diyerek sınıfa dönmek',12,-1),
(12,'"Hadi gidip Eren''e katılalım" diyerek Selin''e önermek',13,6),
(12,'"Selam" deyip Eren''in masasına yakın oturmak',13,4),
(12,'"Canım çekmiyor, gidelim kenara" demek',13,-1),
(13,'"Doğru olanı yapıyorum ve bunda değişecek bir şey yok" demek',14,7),
(13,'Hiçbir şey söylemeden Burak''ın yanından uzaklaşmak',14,3),
(13,'"Bir şey bilmiyorum" diyerek inkar etmek',14,-2),
(14,'Başından beri gördüklerini ayrıntılı anlatmak',15,5),
(14,'Bazı şeyleri söyleyip bazılarını atlamak',15,3),
(14,'"Pek bir şey fark etmedim" deyip geçiştirmek',15,-2),
(15,'"Evet, hazırım. Gördüklerimi eksiksiz anlatırım" demek',16,5),
(15,'"Söyleyebileceklerimi söylerim ama fazla bilmiyorum" demek',17,2),
(15,'"Karışmak istemiyorum, bilmiyorum" demek',18,-2);

-- ============================================================
-- Tablo 8: feedback — Öğrenci geri bildirimleri
-- ============================================================
CREATE TABLE IF NOT EXISTS feedback (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    category    ENUM('konu','platform','oyun','diger') NOT NULL DEFAULT 'diger',
    message     TEXT NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
