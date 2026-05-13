--
-- PostgreSQL database dump
--

-- Dumped from database version 14.5
-- Dumped by pg_dump version 14.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: jenis_pembayaran; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jenis_pembayaran (
    id smallint NOT NULL,
    urutan smallint NOT NULL,
    nama character varying(100) NOT NULL,
    tarif bigint DEFAULT '0'::bigint NOT NULL,
    is_flat boolean DEFAULT true NOT NULL,
    is_aktif boolean DEFAULT true NOT NULL,
    tahun_pelajaran character varying(9) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.jenis_pembayaran OWNER TO postgres;

--
-- Name: jenis_pembayaran_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jenis_pembayaran_id_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jenis_pembayaran_id_seq OWNER TO postgres;

--
-- Name: jenis_pembayaran_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jenis_pembayaran_id_seq OWNED BY public.jenis_pembayaran.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: pengeluaran; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengeluaran (
    id bigint NOT NULL,
    pos_biaya_id bigint NOT NULL,
    user_id bigint NOT NULL,
    tanggal date NOT NULL,
    jumlah bigint NOT NULL,
    bulan smallint NOT NULL,
    tahun integer NOT NULL,
    tahun_pelajaran character varying(9) NOT NULL,
    keterangan text,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.pengeluaran OWNER TO postgres;

--
-- Name: pengeluaran_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pengeluaran_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pengeluaran_id_seq OWNER TO postgres;

--
-- Name: pengeluaran_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pengeluaran_id_seq OWNED BY public.pengeluaran.id;


--
-- Name: pos_biaya; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pos_biaya (
    id bigint NOT NULL,
    nama character varying(100) NOT NULL,
    anggaran bigint NOT NULL,
    tahun_pelajaran character varying(9) NOT NULL,
    keterangan text,
    is_aktif boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.pos_biaya OWNER TO postgres;

--
-- Name: pos_biaya_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pos_biaya_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pos_biaya_id_seq OWNER TO postgres;

--
-- Name: pos_biaya_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pos_biaya_id_seq OWNED BY public.pos_biaya.id;


--
-- Name: saldo_awal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.saldo_awal (
    id smallint NOT NULL,
    tahun_pelajaran character varying(9) NOT NULL,
    jumlah bigint NOT NULL,
    keterangan text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.saldo_awal OWNER TO postgres;

--
-- Name: saldo_awal_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.saldo_awal_id_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saldo_awal_id_seq OWNER TO postgres;

--
-- Name: saldo_awal_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.saldo_awal_id_seq OWNED BY public.saldo_awal.id;


--
-- Name: sekolah; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sekolah (
    id smallint NOT NULL,
    nama_sekolah character varying(150) NOT NULL,
    nama_yayasan character varying(150),
    alamat text,
    telepon character varying(20),
    email character varying(100),
    kepala_tu character varying(100),
    nip_kepala_tu character varying(30),
    tahun_pelajaran character varying(9) NOT NULL,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sekolah OWNER TO postgres;

--
-- Name: sekolah_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sekolah_id_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sekolah_id_seq OWNER TO postgres;

--
-- Name: sekolah_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sekolah_id_seq OWNED BY public.sekolah.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: siswa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.siswa (
    id bigint NOT NULL,
    no_induk character varying(20) NOT NULL,
    nama character varying(100) NOT NULL,
    kelas character varying(10) NOT NULL,
    jenis_kelamin character varying(255) NOT NULL,
    tanggal_masuk date,
    status character varying(255) DEFAULT 'aktif'::character varying NOT NULL,
    tunggakan_awal bigint DEFAULT '0'::bigint NOT NULL,
    tarif_spp bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT siswa_jenis_kelamin_check CHECK (((jenis_kelamin)::text = ANY ((ARRAY['L'::character varying, 'P'::character varying])::text[]))),
    CONSTRAINT siswa_status_check CHECK (((status)::text = ANY ((ARRAY['aktif'::character varying, 'nonaktif'::character varying, 'lulus'::character varying])::text[])))
);


ALTER TABLE public.siswa OWNER TO postgres;

--
-- Name: siswa_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.siswa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.siswa_id_seq OWNER TO postgres;

--
-- Name: siswa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.siswa_id_seq OWNED BY public.siswa.id;


--
-- Name: tagihan_iuran; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tagihan_iuran (
    id bigint NOT NULL,
    siswa_id bigint NOT NULL,
    jenis_pembayaran_id bigint NOT NULL,
    tahun_pelajaran character varying(9) NOT NULL,
    tagihan bigint NOT NULL,
    terbayar bigint DEFAULT '0'::bigint NOT NULL,
    status character varying(255) DEFAULT 'belum'::character varying NOT NULL,
    updated_at timestamp(0) without time zone,
    CONSTRAINT tagihan_iuran_status_check CHECK (((status)::text = ANY ((ARRAY['belum'::character varying, 'cicilan'::character varying, 'lunas'::character varying])::text[])))
);


ALTER TABLE public.tagihan_iuran OWNER TO postgres;

--
-- Name: tagihan_iuran_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tagihan_iuran_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tagihan_iuran_id_seq OWNER TO postgres;

--
-- Name: tagihan_iuran_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tagihan_iuran_id_seq OWNED BY public.tagihan_iuran.id;


--
-- Name: tagihan_spp; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tagihan_spp (
    id bigint NOT NULL,
    siswa_id bigint NOT NULL,
    bulan smallint NOT NULL,
    tahun integer NOT NULL,
    tagihan bigint NOT NULL,
    terbayar bigint DEFAULT '0'::bigint NOT NULL,
    status character varying(255) DEFAULT 'belum'::character varying NOT NULL,
    updated_at timestamp(0) without time zone,
    CONSTRAINT tagihan_spp_status_check CHECK (((status)::text = ANY ((ARRAY['belum'::character varying, 'cicilan'::character varying, 'lunas'::character varying])::text[])))
);


ALTER TABLE public.tagihan_spp OWNER TO postgres;

--
-- Name: tagihan_spp_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tagihan_spp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tagihan_spp_id_seq OWNER TO postgres;

--
-- Name: tagihan_spp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tagihan_spp_id_seq OWNED BY public.tagihan_spp.id;


--
-- Name: transaksi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transaksi (
    id bigint NOT NULL,
    no_transaksi character varying(20) NOT NULL,
    siswa_id bigint NOT NULL,
    user_id bigint NOT NULL,
    tanggal date NOT NULL,
    total_bayar bigint NOT NULL,
    bayar_tunggakan bigint DEFAULT '0'::bigint NOT NULL,
    tahun_pelajaran character varying(9) NOT NULL,
    keterangan text,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.transaksi OWNER TO postgres;

--
-- Name: transaksi_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transaksi_detail (
    id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.transaksi_detail OWNER TO postgres;

--
-- Name: transaksi_detail_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transaksi_detail_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.transaksi_detail_id_seq OWNER TO postgres;

--
-- Name: transaksi_detail_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transaksi_detail_id_seq OWNED BY public.transaksi_detail.id;


--
-- Name: transaksi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transaksi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.transaksi_id_seq OWNER TO postgres;

--
-- Name: transaksi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transaksi_id_seq OWNED BY public.transaksi.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jenis_pembayaran id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_pembayaran ALTER COLUMN id SET DEFAULT nextval('public.jenis_pembayaran_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: pengeluaran id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengeluaran ALTER COLUMN id SET DEFAULT nextval('public.pengeluaran_id_seq'::regclass);


--
-- Name: pos_biaya id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pos_biaya ALTER COLUMN id SET DEFAULT nextval('public.pos_biaya_id_seq'::regclass);


--
-- Name: saldo_awal id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saldo_awal ALTER COLUMN id SET DEFAULT nextval('public.saldo_awal_id_seq'::regclass);


--
-- Name: sekolah id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sekolah ALTER COLUMN id SET DEFAULT nextval('public.sekolah_id_seq'::regclass);


--
-- Name: siswa id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.siswa ALTER COLUMN id SET DEFAULT nextval('public.siswa_id_seq'::regclass);


--
-- Name: tagihan_iuran id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagihan_iuran ALTER COLUMN id SET DEFAULT nextval('public.tagihan_iuran_id_seq'::regclass);


--
-- Name: tagihan_spp id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagihan_spp ALTER COLUMN id SET DEFAULT nextval('public.tagihan_spp_id_seq'::regclass);


--
-- Name: transaksi id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi ALTER COLUMN id SET DEFAULT nextval('public.transaksi_id_seq'::regclass);


--
-- Name: transaksi_detail id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi_detail ALTER COLUMN id SET DEFAULT nextval('public.transaksi_detail_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: jenis_pembayaran jenis_pembayaran_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_pembayaran
    ADD CONSTRAINT jenis_pembayaran_pkey PRIMARY KEY (id);


--
-- Name: jenis_pembayaran jenis_pembayaran_urutan_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jenis_pembayaran
    ADD CONSTRAINT jenis_pembayaran_urutan_unique UNIQUE (urutan);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: pengeluaran pengeluaran_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengeluaran
    ADD CONSTRAINT pengeluaran_pkey PRIMARY KEY (id);


--
-- Name: pos_biaya pos_biaya_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pos_biaya
    ADD CONSTRAINT pos_biaya_pkey PRIMARY KEY (id);


--
-- Name: saldo_awal saldo_awal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saldo_awal
    ADD CONSTRAINT saldo_awal_pkey PRIMARY KEY (id);


--
-- Name: saldo_awal saldo_awal_tahun_pelajaran_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.saldo_awal
    ADD CONSTRAINT saldo_awal_tahun_pelajaran_unique UNIQUE (tahun_pelajaran);


--
-- Name: sekolah sekolah_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sekolah
    ADD CONSTRAINT sekolah_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: siswa siswa_no_induk_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.siswa
    ADD CONSTRAINT siswa_no_induk_unique UNIQUE (no_induk);


--
-- Name: siswa siswa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.siswa
    ADD CONSTRAINT siswa_pkey PRIMARY KEY (id);


--
-- Name: tagihan_iuran tagihan_iuran_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagihan_iuran
    ADD CONSTRAINT tagihan_iuran_pkey PRIMARY KEY (id);


--
-- Name: tagihan_spp tagihan_spp_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagihan_spp
    ADD CONSTRAINT tagihan_spp_pkey PRIMARY KEY (id);


--
-- Name: transaksi_detail transaksi_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi_detail
    ADD CONSTRAINT transaksi_detail_pkey PRIMARY KEY (id);


--
-- Name: transaksi transaksi_no_transaksi_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT transaksi_no_transaksi_unique UNIQUE (no_transaksi);


--
-- Name: transaksi transaksi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT transaksi_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: pengeluaran pengeluaran_pos_biaya_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengeluaran
    ADD CONSTRAINT pengeluaran_pos_biaya_id_foreign FOREIGN KEY (pos_biaya_id) REFERENCES public.pos_biaya(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: pengeluaran pengeluaran_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengeluaran
    ADD CONSTRAINT pengeluaran_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: tagihan_iuran tagihan_iuran_jenis_pembayaran_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagihan_iuran
    ADD CONSTRAINT tagihan_iuran_jenis_pembayaran_id_foreign FOREIGN KEY (jenis_pembayaran_id) REFERENCES public.jenis_pembayaran(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: tagihan_iuran tagihan_iuran_siswa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagihan_iuran
    ADD CONSTRAINT tagihan_iuran_siswa_id_foreign FOREIGN KEY (siswa_id) REFERENCES public.siswa(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: tagihan_spp tagihan_spp_siswa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagihan_spp
    ADD CONSTRAINT tagihan_spp_siswa_id_foreign FOREIGN KEY (siswa_id) REFERENCES public.siswa(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: transaksi transaksi_siswa_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT transaksi_siswa_id_foreign FOREIGN KEY (siswa_id) REFERENCES public.siswa(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: transaksi transaksi_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transaksi
    ADD CONSTRAINT transaksi_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

