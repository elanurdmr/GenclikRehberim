-- Bulmaca kelime bankasına yeni kelimeler ekle (GÖREV 2)
-- max_len sınırı 8'den 10'a çıkarıldı, 10 yeni kelime eklendi

USE db_genclik_rehberim;

INSERT IGNORE INTO crossword_bank (clue, answer, active, sort_order) VALUES
('İnsanları bir arada tutan toplumsal bağ.',    'DAYANIŞMA',  1, 100),
('Farklılıklara hoşgörüyle bakma.',             'HOŞGÖRÜ',    1, 101),
('Kendine olan inanç ve güven.',                'ÖZGÜVEN',    1, 102),
('Zor anlarda yılmadan devam etmek.',           'KARARLIL',   1, 103),
('Birinin acısını hissedip üzülme.',            'MERHAMETLİ', 1, 104),
('Birden fazla kişinin birlikte çalışması.',    'İŞBİRLİĞİ', 1, 105),
('Başkalarını etkileyen, yol gösteren kişi.',  'LİDER',      1, 106),
('Yanlışı kabul edip özür dilemek.',           'ÖZÜR',       1, 107),
('Herkesin eşit haklara sahip olması.',        'EŞİTLİK',   1, 108),
('Başkasının başarısından sevinmek.',          'TEBRİK',     1, 109);
