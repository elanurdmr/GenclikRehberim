-- migration_benimhikayem_v2.sql
-- Hikaye senaryolarını güncel içerikle yeniden yükler.
-- Önkoşul: migration_benimhikayem.sql çalıştırılmış olmalı.
USE db_genclik_rehberim;

-- 1) Bitiş düğümlerine bonus puan desteği ekle
ALTER TABLE story_nodes
  ADD COLUMN IF NOT EXISTS bonus_points TINYINT NOT NULL DEFAULT 0
  AFTER feedback;

-- 2) Eski senaryo verisini temizle (FK sırası: choices → nodes)
DELETE FROM story_choices;
DELETE FROM story_nodes;

-- 3) Yeni aktivite max_score değerini güncelle
--    (tüm A: ~90 puan + bitiş bonusu +10 = 100)
UPDATE activities SET max_score = 100 WHERE type = 'benimhikayem';

-- ============================================================
-- HİKAYE 1: Okul Kantini ve Dijital Dedikodu (Genişletilmiş)
-- 15 karar düğümü (doğrusal) + 3 bitiş = 18 düğüm
-- Oyuncu her oynamada tam 15 seçim yapıyor.
-- Tüm A: 90+10 = 100 | Tüm B: ~53 | Tüm C: ~-27 → 0
-- C seçimleri yalnızca -1/-2 ceza: yanlış yapan öğrenci de makul puan alır.
-- ============================================================

INSERT INTO story_nodes (id, scenario, text, type, feedback, bonus_points) VALUES
(1, 1,
 'Öğle arasında kantin sırasındasın. Bu hafta sınıfa nakil olan Eren hemen arkanda duruyor. Tam sıraya girecekken yanındaki arkadaşın Burak yüksek sesle: "Eski okulunda çanta çalmış biri var sıramızda, dikkat edin!" diyor. Birkaç öğrenci gülüşüyor. Eren''in yüzü kıpkırmızıya dönüyor.',
 'start', NULL, 0),
(2, 1,
 'Kantinde herkes yer aldı. Eren köşede yalnız bir masada oturuyor; yanında hiç kimse yok. Burak sana sesleniyor: "Hadi gel bizimle otur." Yan masaya bakıyorsun; Eren tabağına bile bakmadan yemeğini yemeye çalışıyor.',
 'middle', NULL, 0),
(3, 1,
 'Öğleden sonra teneffüste koridorda bir kalabalık toplandı. Burak ve iki arkadaşı Eren''in sırt çantasını kapıp içindekileri yere döküyor. "Çalıntı bir şey var mı bak!" diye bağırıyorlar. Öğretmen koridorun diğer ucunda.',
 'middle', NULL, 0),
(4, 1,
 'Son ders: öğretmen fen projesi için gruplar kurdu. Herkes birini seçti; Eren hâlâ gruba dahil değil, kenarda bekliyor. Öğretmen "Herkes bir gruba girsin" dedi ve sana bakıyor.',
 'middle', NULL, 0),
(5, 1,
 'Okul çıkışında çantanı alıp kapıya yürürken Burak seni durdurdu. Alçak sesle: "Bugün Eren''i korumaya mı çalıştın? Dikkat et, o grupla değilsen yalnız kalırsın." Diğer arkadaşlar uzaktan sizi izliyor.',
 'middle', NULL, 0),
(6, 1,
 'Eve gelince telefona bakıyorsun. Sınıf grubuna Burak''ın Eren''in kötü bir fotoğrafını atıp üzerine alaylı yazılar yazdığını görüyorsun. 24 kişi beğenmiş. Arkadaşın Selin sana "Sence komik mi?" diye mesaj attı.',
 'middle', NULL, 0),
(7, 1,
 'Saat 23.00. Paylaşım yayılmaya devam ediyor; yorumlar giderek sertleşiyor. Telefonunda okul yönetimi uygulaması açık ve "Yetkiliye Bildir" butonu gözüne çarpıyor. Uyuyamıyorsun.',
 'middle', NULL, 0),
(8, 1,
 'Ertesi sabah okula geldin. Eren de var ama gözleri şişmiş, kimseyle konuşmuyor. Koridorda Burak ve arkadaşları Eren''i görünce fısıldaşıp gülüyor. Eren adımlarını yavaşlattı, sanki geri dönmek istiyor.',
 'middle', NULL, 0),
(9, 1,
 'Birinci ders bitti. Teneffüste koridora çıktığında Eren''in tuvalet kapısının yanında yerde oturduğunu görüyorsun; kitap tutuyor ama sayfayı çevirmeden bakıyor. Çevresinde kimse yok.',
 'middle', NULL, 0),
(10, 1,
 'Öğleden önce rehber öğretmen Sevgi Hanım sınıfa girdi: "Bazı olaylardan haberdar oldum; bu davranışlar zorbalıktır ve okul bunu ciddiye alıyor." Burak sandalyesinde kıpırdıyor. Sevgi Hanım "Gören veya bilen arkadaşlar benimle konuşabilir" dedi.',
 'middle', NULL, 0),
(11, 1,
 'Öğle arasında Selin sana koşarak geldi: "Eren bugün son kez okula geldiğini söylüyor, okul değiştireceğini söylüyor. Artık dayanamıyormuş." Bahçeye bakıyorsun; Eren tek başına bir bankta oturuyor. Derslere 10 dakika var.',
 'middle', NULL, 0),
(12, 1,
 'Öğle arası kantindeysin. Selin''le birlikte sıraya girdiniz. Eren de kantinde; yine tek başına köşe masaya yürüyor. Gözlerinin altında mor halkalar var, dün olmayan yüz ifadesi bu: tamamen kapalı.',
 'middle', NULL, 0),
(13, 1,
 'Öğle arası biterken Burak seni koridorda köşeye çekti: "Rehber öğretmene bir şey mi söyledin? Olaylar büyümesin istiyorsan sus. Şimdi karar ver — bizden misin değil misin?" Çantanı sıkıca tutuyorsun.',
 'middle', NULL, 0),
(14, 1,
 'Dersten önce Sevgi Hanım seni kapıda bekliyordu: "Müdür bey aile toplantısı düzenleyecek. Sana birkaç soru sormak istiyorum; o günlerde neler yaşandığını bana anlatabilir misin?" Defterini açıp not almaya hazır.',
 'middle', NULL, 0),
(15, 1,
 'Müdür odasının kapısı önünde Sevgi Hanım seni karşıladı: "İçeride Eren''in ailesi ve Burak''ın ailesi var. Tanıklığın bu toplantının seyrini belirleyecek. Hazır mısın?"',
 'middle', NULL, 0),
-- Bitiş 1: Efsanevi (Node 15 → A)
(16, 1,
 'Tanıklığın toplantının seyrini değiştirdi. Okul disiplin kurulu harekete geçti; Burak ailesiyle birlikte Eren''den özür dilemek zorunda kaldı. Fotoğraf tüm platformlardan kaldırıldı. Eren okul değiştirmedi ve senin sayende sınıfa gerçek anlamda dahil oldu. Sevgi Hanım seni "Empatinin Gücü" panosunda tebrik etti.',
 'end',
 'Empati ve cesaret bir araya geldiğinde mucizeler yaratır. Her kararınla hem Eren''in hayatını hem de sınıfın iklimini değiştirdin. Bu dünyayı daha iyi bir yer yapıyorsun!',
 10),
-- Bitiş 2: İyi (Node 15 → B)
(17, 1,
 'Kısmi tanıklığın bir şeyler değiştirdi. Burak uyarıldı, paylaşım kaldırıldı. Eren zorluklarla da olsa okulda kalmaya karar verdi. Mükemmel olmak zorunda değilsin; harekete geçmeye çalışmak başlı başına değerlidir.',
 'end',
 'Doğruyu yapmak her zaman kolay değildir. Önemli olan harekete geçmeyi denemektir. Bir dahaki seferde biraz daha cesur olmayı dene — adımların küçük bile olsa fark yaratır.',
 0),
-- Bitiş 3: Pasif / Kötü (Node 15 → C)
(18, 1,
 'Suskunluğun bu sefer pahalıya patlıyor. Yeterli tanıklık olmadığı için toplantı sonuçsuz kaldı. Eren hafta sonu okul değiştirdi. O bankta yalnız oturan çocuğun yüzü uzun süre aklından çıkmayacak.',
 'end',
 'Bazen en büyük zorbalık, yapılan kötülüğe sessiz kalmaktır. Bir dahaki seferde sesini yükseltmeye cesaret edebilirsin. Artık ne yapman gerektiğini biliyorsun — bu farkındalık çok değerlidir.',
 0);

-- Seçimler — Node 1–14: her seçim bir sonraki düğüme gider (doğrusal)
--             Node 15: A→16, B→17, C→18
-- Puanlama: A = +5…+7, B = +3…+4, C = -1…-2  (yanlış yapan öğrenci yine de makul puan biriktirir)
INSERT INTO story_choices (node_id, choice_text, next_node_id, points) VALUES
-- 1
(1,  '"Bu doğru değil, kanıtın var mı?" diyerek Eren''i savunmak',                              2,  7),
(1,  'Sessizce sıradan çıkmak, karışmamak',                                                     2,  3),
(1,  'Gülerek arkadaşlarına katılmak',                                                          2, -2),
-- 2
(2,  '"Hep beraber oturalım, Eren de gelsin" demek',                                            3,  6),
(2,  'Burak''ın masasına gitmek ama Eren''e gülümseyerek göz kırpmak',                         3,  4),
(2,  'Hiçbir şey demeden Burak''ın yanına oturmak',                                            3, -2),
-- 3
(3,  'Hemen öğretmene koşmak ve durumu anlatmak',                                              4,  7),
(3,  '"Dur!" diye bağırıp öne çıkmak',                                                         4,  5),
(3,  'Uzaktan seyretmek, araya girmemek',                                                       4, -2),
-- 4
(4,  '"Eren bizimle gelsin" demek',                                                             5,  6),
(4,  'Öğretmene fısıldayarak Eren''in gruba alınmasını istemek',                               5,  4),
(4,  'Sessiz kalmak, öğretmen başka çözüm bulsun diye beklemek',                                5, -2),
-- 5: Burak baskısı
(5,  '"Doğru gördüğüm şeyleri yapıyorum, bu değişmez" demek',                                  6,  7),
(5,  'Omuz silkip sükunetle yürümeye devam etmek',                                             6,  3),
(5,  '"Tamam tamam, bir daha karışmam" deyip geçmek',                                          6, -2),
-- 6: Akşam
(6,  'Selin''e "Bu siber zorbalık, ekran görüntüsünü alıp bildirmeliyiz" demek',               7,  7),
(6,  '"Yanlış ama ne yapabilirim ki" yazmak',                                                   7,  3),
(6,  '"Gerçekten çok komik :)" yazıp paylaşımı beğenmek',                                      7, -2),
-- 7: Gece
(7,  '"Yetkiliye Bildir" butonuna basmak ve ekran görüntüsünü eklemek',                        8,  6),
(7,  'Burak''a özel mesaj atıp "Fazla ileri gidiyorsun, kaldır onu" demek',                    8,  4),
(7,  'Telefonu kapatıp uyumaya çalışmak',                                                       8, -1),
-- 8: Ertesi sabah
(8,  'Eren''in yanına gitmek ve "Merhaba, nasılsın?" demek',                                   9,  5),
(8,  '"Günaydın" deyip kapıyı tutmak, içeri birlikte girmek',                                  9,  3),
(8,  'Fark etmeden sınıfa girmek',                                                              9, -1),
-- 9: Sabah teneffüsü
(9,  '"Merhaba, yalnız mısın? Yanına oturabilir miyim?" demek',                                10,  6),
(9,  'Gülerek baş sallayıp geçmek, en azından görmezden gelmemek',                            10,  4),
(9,  'Başka yöne bakmak',                                                                       10, -1),
-- 10: Rehberlik saati
(10, 'Dersten sonra Sevgi Hanım''ın odasına gidip her şeyi anlatmak',                          11,  7),
(10, 'Okulun anonim ihbar kutusuna not bırakmak',                                               11,  4),
(10, '"Herkes kendi bileceği iş" diyerek bir şey yapmamak',                                    11, -2),
-- 11: Selin''in haberi
(11, 'Hemen bahçeye gidip Eren''in yanına oturmak',                                            12,  6),
(11, 'Selin ile birlikte gidip ikisi de destek vermek',                                         12,  4),
(11, '"Ben ne yapabilirim ki" diyerek sınıfa dönmek',                                          12, -1),
-- 12: Öğle arası kantin 2. gün
(12, '"Hadi gidip Eren''e katılalım" diyerek Selin''e önermek',                                13,  6),
(12, '"Selam" deyip Eren''in masasına yakın oturmak',                                          13,  4),
(12, '"Canım çekmiyor, gidelim kenara" demek',                                                  13, -1),
-- 13: Burak baskısı 2
(13, '"Doğru olanı yapıyorum ve bunda değişecek bir şey yok" demek',                           14,  7),
(13, 'Hiçbir şey söylemeden Burak''ın yanından uzaklaşmak',                                    14,  3),
(13, '"Bir şey bilmiyorum" diyerek inkar etmek',                                               14, -2),
-- 14: Ön görüşme
(14, 'Başından beri gördüklerini ayrıntılı anlatmak',                                          15,  5),
(14, 'Bazı şeyleri söyleyip bazılarını atlamak',                                               15,  3),
(14, '"Pek bir şey fark etmedim" deyip geçiştirmek',                                          15, -2),
-- 15: Final
(15, '"Evet, hazırım. Gördüklerimi eksiksiz anlatırım" demek',                                 16,  5),
(15, '"Söyleyebileceklerimi söylerim ama fazla bilmiyorum" demek',                              17,  2),
(15, '"Karışmak istemiyorum, bilmiyorum" demek',                                               18, -2);
