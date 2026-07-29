-- Sequence and defined type
CREATE SEQUENCE IF NOT EXISTS migrations_id_seq;
CREATE SEQUENCE IF NOT EXISTS users_id_seq;
CREATE SEQUENCE IF NOT EXISTS transactions_id_seq;
CREATE SEQUENCE IF NOT EXISTS transaction_items_id_seq;
CREATE SEQUENCE IF NOT EXISTS shoes_id_seq;

DROP TABLE IF EXISTS "public"."migrations";
-- Table Definition
CREATE TABLE "public"."migrations" (
    "id" int4 NOT NULL DEFAULT nextval('migrations_id_seq'::regclass),
    "migration" varchar(191) NOT NULL,
    "batch" int4 NOT NULL,
    PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "public"."users";
-- Table Definition
CREATE TABLE "public"."users" (
    "id" int8 NOT NULL DEFAULT nextval('users_id_seq'::regclass),
    "name" varchar(191) NOT NULL,
    "email" varchar(191) NOT NULL,
    "email_verified_at" timestamp(0),
    "password" varchar(191) NOT NULL,
    "remember_token" varchar(100),
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    PRIMARY KEY ("id")
);


-- Indices
CREATE UNIQUE INDEX users_email_unique ON public.users USING btree (email);

DROP TABLE IF EXISTS "public"."transactions";
-- Table Definition
CREATE TABLE "public"."transactions" (
    "id" int8 NOT NULL DEFAULT nextval('transactions_id_seq'::regclass),
    "customer_name" varchar(191) NOT NULL,
    "customer_phone" varchar(191) NOT NULL,
    "shipping_address" text NOT NULL,
    "shipping_courier" varchar(191) NOT NULL,
    "latitude" numeric(10,8),
    "longitude" numeric(11,8),
    "total_price" numeric(12,2) NOT NULL,
    "status" varchar(191) NOT NULL DEFAULT 'pending'::character varying,
    "payment_proof" varchar(191),
    "user_id" int8,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "public"."transaction_items";
-- Table Definition
CREATE TABLE "public"."transaction_items" (
    "id" int8 NOT NULL DEFAULT nextval('transaction_items_id_seq'::regclass),
    "transaction_id" int8 NOT NULL,
    "shoe_id" int8 NOT NULL,
    "shoe_size" int4 NOT NULL,
    "quantity" int4 NOT NULL,
    "price" numeric(12,2) NOT NULL,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    CONSTRAINT "transaction_items_shoe_id_foreign" FOREIGN KEY ("shoe_id") REFERENCES "public"."shoes"("id") ON DELETE CASCADE,
    CONSTRAINT "transaction_items_transaction_id_foreign" FOREIGN KEY ("transaction_id") REFERENCES "public"."transactions"("id") ON DELETE CASCADE,
    PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS "public"."shoes";
-- Table Definition
CREATE TABLE "public"."shoes" (
    "id" int8 NOT NULL DEFAULT nextval('shoes_id_seq'::regclass),
    "name" varchar(191) NOT NULL,
    "brand" varchar(191) NOT NULL,
    "category" varchar(191) NOT NULL,
    "price" numeric(12,2) NOT NULL,
    "sizes" json NOT NULL,
    "image_url" varchar(191),
    "description" text,
    "stock" int4 NOT NULL DEFAULT 0,
    "created_at" timestamp(0),
    "updated_at" timestamp(0),
    PRIMARY KEY ("id")
);

INSERT INTO "public"."migrations" ("id", "migration", "batch") VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2026_07_26_000001_create_shoes_table', 1),
(3, '2026_07_26_000002_create_transactions_table', 1),
(4, '2026_07_26_000003_create_transaction_items_table', 1);
INSERT INTO "public"."users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at") VALUES
(1, 'Admin SoleSteps', 'admin@solesteps.com', NULL, '$2y$12$meczFnBgAohUzN3u3hD5UOvdv2d9Uqhe1hZkx1K.k.vX2pRoGaeSW', NULL, '2026-07-26 21:00:17', '2026-07-29 06:17:02'),
(2, 'Budi Santoso', 'customer@gmail.com', NULL, '$2y$12$vHyToKwwlKjhQ06TLqHq2eMnc2bNzhFQNp5mUCKO0eDvXNuujJXui', NULL, '2026-07-26 21:00:18', '2026-07-29 06:17:08'),
(3, 'rei', 'xireikikuromiru@gmail.com', NULL, '$2y$12$d2HU1e27D1LRZJRyxBeulOhZqiTdiOESs2YydtBrXA/l604ndaZ2a', NULL, '2026-07-28 10:15:16', '2026-07-28 10:15:16'),
(4, 'rei', 'xireikikuromori@gmail.com', NULL, '$2y$12$8dpTfUlvRoA2q4JnlKwC/.11VMNX1H2qvXHQnWabHVUNswOyZxs72', NULL, '2026-07-28 10:16:06', '2026-07-28 10:16:06'),
(5, 'bagpao', 'bagpaorebus@gmail.com', NULL, '$2y$12$Az2857tizkOzjs.zqj5gHeYGdDbkhXLx7BxZWUaMhXhpkJ.Y8lA.e', NULL, '2026-07-29 10:31:40', '2026-07-29 10:31:40');
INSERT INTO "public"."transactions" ("id", "customer_name", "customer_phone", "shipping_address", "shipping_courier", "latitude", "longitude", "total_price", "status", "payment_proof", "user_id", "created_at", "updated_at") VALUES
(1, 'rei', '081234567890', 'jajan', 'Pos Indonesia', -6.24451200, 106.84551100, 1914000.00, 'pending', 'uploads/receipts/1785252767_receipt_6a68cb9f8e0d3.jpg', 4, '2026-07-28 22:32:47', '2026-07-28 22:32:47'),
(2, 'rei', '081234567890', 'awawa', 'JNE', -7.84256090, 110.37707970, 1814000.00, 'pending', 'uploads/receipts/1785263494_receipt_6a68f5862b38d.jpg', 4, '2026-07-29 01:31:34', '2026-07-29 01:31:34'),
(3, 'rei', '081234567890', 'pangeran, jogjakarta rt 2,rw 9 no 26', 'J&T', -6.24451200, 106.84551100, 1564000.00, 'approved', 'uploads/receipts/1785283917_receipt_6a69454dc640a.jpg', 4, '2026-07-29 07:11:57', '2026-07-29 07:12:40'),
(4, 'bagpao', '081234567890', 'pangeran rt 8, re 5', 'JNE - Express', -7.84252360, 110.37705180, 2215000.00, 'approved', 'uploads/receipts/1785296046_receipt_6a6974ae104ff.jpg', 5, '2026-07-29 10:34:06', '2026-07-29 10:35:49');
INSERT INTO "public"."transaction_items" ("id", "transaction_id", "shoe_id", "shoe_size", "quantity", "price", "created_at", "updated_at") VALUES
(2, 2, 4, 40, 1, 1799000.00, '2026-07-29 01:31:35', '2026-07-29 01:31:35'),
(3, 3, 1, 42, 1, 1549000.00, '2026-07-29 07:11:59', '2026-07-29 07:11:59'),
(4, 4, 10, 42, 1, 2200000.00, '2026-07-29 10:34:07', '2026-07-29 10:34:07');
INSERT INTO "public"."shoes" ("id", "name", "brand", "category", "price", "sizes", "image_url", "description", "stock", "created_at", "updated_at") VALUES
(1, 'Nike Air Force 1 Low', 'Nike', 'Sneakers', 1549000.00, '[39,40,41,42,43]', 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600', 'Sepatu legendaris Nike Air Force 1. Menampilkan siluet klasik berpadu dengan bahan kulit premium berwarna putih bersih yang tahan lama serta bantalan udara Air Sole untuk kenyamanan sepanjang hari.', 14, '2026-07-26 21:00:18', '2026-07-29 07:12:41'),
(2, 'Adidas Ultraboost Light', 'Adidas', 'Running', 2799000.00, '[40,41,42,43,44]', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600', 'Adidas Ultraboost Light adalah sepatu lari teringan yang pernah ada dengan bantalan Boost legendaris. Dilengkapi upper Primeknit yang melukis kaki Anda serta cengkeraman Continental Rubber yang tangguh.', 12, '2026-07-26 21:00:18', '2026-07-26 21:00:18'),
(3, 'Air Jordan 1 Retro High Chicago', 'Jordan', 'Basketball', 3099000.00, '[41,42,43,44,45]', 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600', 'Air Jordan 1 Retro High klasik Chicago dengan warna ikonik merah, hitam, dan putih. Sepatu basket ikonik perpaduan sempurna antara warisan olahraga legendaris Michael Jordan dan gaya streetwear masa kini.', 6, '2026-07-26 21:00:18', '2026-07-26 21:00:18'),
(4, 'Puma RS-X Geek Unisex', 'Puma', 'Casual', 1799000.00, '[39,40,41,42,43]', 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600', 'Gaya retro-futuristik bersatu kembali dalam Puma RS-X Geek. Mengusung teknologi bantalan Running System (RS) berbalut desain sol tebal chunky dan kombinasi warna modern yang memikat.', 18, '2026-07-26 21:00:19', '2026-07-26 21:00:19'),
(9, 'Sepatu Handball Spezial', 'Adidas', 'Casual', 1900000.00, '["41"]', 'uploads/shoes/1785264802_6a68faa29100e.jpg', 'Sneaker adidas yang stylish dengan pesona retro-modern.
Sepatu adidas Handball Spezial ini menjadi andalanmu untuk tampil dengan style kasual. Awalnya didesain untuk bola tangan di dalam ruangan di tahun 1979, sepatu low-profile ini kini dapat dibawa ke mana saja dari pinggir lapangan ke aktivitas kasual. Upper berbahan suede premium dengan detail kulit lembut dan sol gom menghadirkan tampilan yang stylish tetapi sporty pada desain yang ikonis. Sentuhan samar seperti branding berwarna emas metalik di bagian samping mengapresiasi asal-usul atletik mereka. Padukan dengan pakaian apa pun, mulai dari denim hingga dress untuk style klasik dan serbaguna yang menjembatani nostalgia terbaik saat ini.', 10, '2026-07-29 01:53:22', '2026-07-29 01:53:42'),
(10, 'Sepatu Samba OG', 'Adidas', 'Casual', 2200000.00, '["42"]', 'uploads/shoes/1785279767_6a69351753d61.jpg', 'SAMBA ORIGINALS
Diciptakan untuk sepak bola indoor, Samba menjadi ikon street style yang tak lekang oleh waktu. Desain ini tetap mempertahankan warisannya dengan upper low-profile yang stylish dan lembut dari bahan kulit, overlay suede, dan sol gom, menjadikannya sepatu favorit di lemari setiap orang - di dalam dan di luar lapangan.', 9, '2026-07-29 06:02:47', '2026-07-29 10:35:50'),
(12, 'Sepatu Hiking TERREX Freehiker SL GORE-TEX', 'Adidas', 'Running', 230000000.00, '["38","39","40","41","42"]', 'uploads/shoes/1785296250_6a69757a2bc15.jpg', 'Sepatu dengan bantalan Dreamstrike untuk kenyamanan.
Sepatu Hiking Terrex Freehiker SL GORE-TEX® adalah pintu gerbangmu menuju petualangan. Baik saat menjelajahi jalur terjal atau berjalan-jalan di kota, sepatu ini membantumu melakukannya dengan nyaman dan tetap stylish.

Midsole Dreamstrike menawarkan kenyamanan di berbagai medan. Membran GORE-TEX yang kedap air membantu menjaga kakimu tetap kering. Outsole karet Continental™ dengan traksi yang kuat memberikan daya tarik luar biasa baik di trail maupun jalanan kota.

Dengan komitmen adidas terhadap kualitas dan performa, sepatu hiking ini dibuat untuk mendukung langkahmu. Rasakan kebebasan menjelajah dan nikmati waktu di outdoor dengan percaya diri.', 78, '2026-07-29 10:37:30', '2026-07-29 10:37:30');
