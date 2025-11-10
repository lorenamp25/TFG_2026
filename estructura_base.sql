--
-- PostgreSQL database dump
--

\restrict XzQuWXMWM3G48nhBacf17LTGgugtjYHRcetkPKs71UUPGVXiKzl7NgGokx5Od2I

-- Dumped from database version 17.6 (Debian 17.6-1.pgdg13+1)
-- Dumped by pg_dump version 17.6 (Debian 17.6-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
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
-- Name: categorias; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.categorias (
    id integer NOT NULL,
    nombre character varying(255) NOT NULL
);


ALTER TABLE public.categorias OWNER TO receta;

--
-- Name: categorias_id_seq; Type: SEQUENCE; Schema: public; Owner: receta
--

CREATE SEQUENCE public.categorias_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categorias_id_seq OWNER TO receta;

--
-- Name: categorias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: receta
--

ALTER SEQUENCE public.categorias_id_seq OWNED BY public.categorias.id;


--
-- Name: comentarios; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.comentarios (
    id integer NOT NULL,
    receta_id integer,
    usuario_id integer,
    contenido text NOT NULL,
    puntuacion integer,
    fecha_creacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.comentarios OWNER TO receta;

--
-- Name: comentarios_id_seq; Type: SEQUENCE; Schema: public; Owner: receta
--

CREATE SEQUENCE public.comentarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.comentarios_id_seq OWNER TO receta;

--
-- Name: comentarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: receta
--

ALTER SEQUENCE public.comentarios_id_seq OWNED BY public.comentarios.id;


--
-- Name: ingredientes; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.ingredientes (
    id integer NOT NULL,
    nombre character varying(255) NOT NULL
);


ALTER TABLE public.ingredientes OWNER TO receta;

--
-- Name: ingredientes_id_seq; Type: SEQUENCE; Schema: public; Owner: receta
--

CREATE SEQUENCE public.ingredientes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ingredientes_id_seq OWNER TO receta;

--
-- Name: ingredientes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: receta
--

ALTER SEQUENCE public.ingredientes_id_seq OWNED BY public.ingredientes.id;


--
-- Name: mensajes; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.mensajes (
    id integer NOT NULL,
    remitente integer,
    destinatario integer,
    asunto character varying(255),
    contenido text NOT NULL,
    fecha_envio timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    leido boolean DEFAULT false
);


ALTER TABLE public.mensajes OWNER TO receta;

--
-- Name: mensajes_id_seq; Type: SEQUENCE; Schema: public; Owner: receta
--

CREATE SEQUENCE public.mensajes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mensajes_id_seq OWNER TO receta;

--
-- Name: mensajes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: receta
--

ALTER SEQUENCE public.mensajes_id_seq OWNED BY public.mensajes.id;


--
-- Name: receta_ingredientes; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.receta_ingredientes (
    receta_id integer NOT NULL,
    ingrediente_id integer NOT NULL,
    cantidad character varying(64),
    unidad character varying(64)
);


ALTER TABLE public.receta_ingredientes OWNER TO receta;

--
-- Name: receta_instrucciones; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.receta_instrucciones (
    receta_id integer NOT NULL,
    orden integer NOT NULL,
    descripcion text,
    imagen_url character varying(1024)
);


ALTER TABLE public.receta_instrucciones OWNER TO receta;

--
-- Name: recetas; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.recetas (
    id integer NOT NULL,
    titulo character varying(255) NOT NULL,
    descripcion text,
    tiempo_preparacion integer,
    dificultad character varying(50),
    categoria integer,
    imagen_url character varying(1024),
    usuario_id integer
);


ALTER TABLE public.recetas OWNER TO receta;

--
-- Name: recetas_id_seq; Type: SEQUENCE; Schema: public; Owner: receta
--

CREATE SEQUENCE public.recetas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.recetas_id_seq OWNER TO receta;

--
-- Name: recetas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: receta
--

ALTER SEQUENCE public.recetas_id_seq OWNED BY public.recetas.id;


--
-- Name: solicitudes_cambio; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.solicitudes_cambio (
    id integer NOT NULL,
    usuario_id integer,
    receta_id integer,
    descripcion text,
    estado character varying(20) DEFAULT 'pendiente'::character varying,
    fecha_solicitud timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion timestamp without time zone
);


ALTER TABLE public.solicitudes_cambio OWNER TO receta;

--
-- Name: solicitudes_cambio_id_seq; Type: SEQUENCE; Schema: public; Owner: receta
--

CREATE SEQUENCE public.solicitudes_cambio_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.solicitudes_cambio_id_seq OWNER TO receta;

--
-- Name: solicitudes_cambio_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: receta
--

ALTER SEQUENCE public.solicitudes_cambio_id_seq OWNED BY public.solicitudes_cambio.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: receta
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nickname character varying(100),
    nombre character varying(100),
    apellido character varying(100),
    email character varying(255),
    password character varying(255),
    fecha_nacimiento date,
    puntuacion integer DEFAULT 0
);


ALTER TABLE public.usuarios OWNER TO receta;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: receta
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO receta;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: receta
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: categorias id; Type: DEFAULT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.categorias ALTER COLUMN id SET DEFAULT nextval('public.categorias_id_seq'::regclass);


--
-- Name: comentarios id; Type: DEFAULT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.comentarios ALTER COLUMN id SET DEFAULT nextval('public.comentarios_id_seq'::regclass);


--
-- Name: ingredientes id; Type: DEFAULT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.ingredientes ALTER COLUMN id SET DEFAULT nextval('public.ingredientes_id_seq'::regclass);


--
-- Name: mensajes id; Type: DEFAULT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.mensajes ALTER COLUMN id SET DEFAULT nextval('public.mensajes_id_seq'::regclass);


--
-- Name: recetas id; Type: DEFAULT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.recetas ALTER COLUMN id SET DEFAULT nextval('public.recetas_id_seq'::regclass);


--
-- Name: solicitudes_cambio id; Type: DEFAULT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.solicitudes_cambio ALTER COLUMN id SET DEFAULT nextval('public.solicitudes_cambio_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Name: categorias categorias_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_pkey PRIMARY KEY (id);


--
-- Name: comentarios comentarios_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.comentarios
    ADD CONSTRAINT comentarios_pkey PRIMARY KEY (id);


--
-- Name: ingredientes ingredientes_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.ingredientes
    ADD CONSTRAINT ingredientes_pkey PRIMARY KEY (id);


--
-- Name: mensajes mensajes_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.mensajes
    ADD CONSTRAINT mensajes_pkey PRIMARY KEY (id);


--
-- Name: receta_ingredientes receta_ingredientes_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.receta_ingredientes
    ADD CONSTRAINT receta_ingredientes_pkey PRIMARY KEY (receta_id, ingrediente_id);


--
-- Name: receta_instrucciones receta_instrucciones_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.receta_instrucciones
    ADD CONSTRAINT receta_instrucciones_pkey PRIMARY KEY (receta_id, orden);


--
-- Name: recetas recetas_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_pkey PRIMARY KEY (id);


--
-- Name: solicitudes_cambio solicitudes_cambio_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.solicitudes_cambio
    ADD CONSTRAINT solicitudes_cambio_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- Name: usuarios usuarios_nickname_key; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_nickname_key UNIQUE (nickname);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: comentarios comentarios_receta_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.comentarios
    ADD CONSTRAINT comentarios_receta_id_fkey FOREIGN KEY (receta_id) REFERENCES public.recetas(id) ON DELETE CASCADE;


--
-- Name: comentarios comentarios_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.comentarios
    ADD CONSTRAINT comentarios_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: mensajes mensajes_destinatario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.mensajes
    ADD CONSTRAINT mensajes_destinatario_fkey FOREIGN KEY (destinatario) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: mensajes mensajes_remitente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.mensajes
    ADD CONSTRAINT mensajes_remitente_fkey FOREIGN KEY (remitente) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: receta_ingredientes receta_ingredientes_ingrediente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.receta_ingredientes
    ADD CONSTRAINT receta_ingredientes_ingrediente_id_fkey FOREIGN KEY (ingrediente_id) REFERENCES public.ingredientes(id) ON DELETE CASCADE;


--
-- Name: receta_ingredientes receta_ingredientes_receta_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.receta_ingredientes
    ADD CONSTRAINT receta_ingredientes_receta_id_fkey FOREIGN KEY (receta_id) REFERENCES public.recetas(id) ON DELETE CASCADE;


--
-- Name: receta_instrucciones receta_instrucciones_receta_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.receta_instrucciones
    ADD CONSTRAINT receta_instrucciones_receta_id_fkey FOREIGN KEY (receta_id) REFERENCES public.recetas(id) ON DELETE CASCADE;


--
-- Name: recetas recetas_categoria_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_categoria_fkey FOREIGN KEY (categoria) REFERENCES public.categorias(id);


--
-- Name: recetas recetas_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.recetas
    ADD CONSTRAINT recetas_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id);


--
-- Name: solicitudes_cambio solicitudes_cambio_receta_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.solicitudes_cambio
    ADD CONSTRAINT solicitudes_cambio_receta_id_fkey FOREIGN KEY (receta_id) REFERENCES public.recetas(id) ON DELETE SET NULL;


--
-- Name: solicitudes_cambio solicitudes_cambio_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: receta
--

ALTER TABLE ONLY public.solicitudes_cambio
    ADD CONSTRAINT solicitudes_cambio_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict XzQuWXMWM3G48nhBacf17LTGgugtjYHRcetkPKs71UUPGVXiKzl7NgGokx5Od2I