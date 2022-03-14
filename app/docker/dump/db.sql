-- NOTE:
--
-- File paths need to be edited. Search for $$PATH$$ and
-- replace it with the path to the directory containing
-- the extracted data files.
--
--
-- PostgreSQL database dump
--

-- Dumped from database version 12.8 (Debian 12.8-1.pgdg110+1)
-- Dumped by pg_dump version 12.8 (Debian 12.8-1.pgdg110+1)

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

DROP DATABASE postgres;
--
-- Name: postgres; Type: DATABASE; Schema: -; Owner: user
--

CREATE DATABASE postgres WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.utf8' LC_CTYPE = 'en_US.utf8';


ALTER DATABASE postgres OWNER TO "user";

\connect postgres

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

--
-- Name: DATABASE postgres; Type: COMMENT; Schema: -; Owner: user
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: oauth_access_tokens; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.oauth_access_tokens (
    id character varying(255) NOT NULL,
    user_id integer NOT NULL,
    client_id integer NOT NULL,
    scopes jsonb,
    revoked boolean DEFAULT false,
    expires_at timestamp without time zone
);


ALTER TABLE public.oauth_access_tokens OWNER TO "user";

--
-- Name: oauth_auth_codes; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.oauth_auth_codes (
    id integer NOT NULL,
    user_id integer NOT NULL,
    client_id integer NOT NULL,
    scopes jsonb,
    revoked boolean,
    expires_at timestamp without time zone
);


ALTER TABLE public.oauth_auth_codes OWNER TO "user";

--
-- Name: oauth_auth_codes_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.oauth_auth_codes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oauth_auth_codes_id_seq OWNER TO "user";

--
-- Name: oauth_auth_codes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.oauth_auth_codes_id_seq OWNED BY public.oauth_auth_codes.id;


--
-- Name: oauth_clients; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.oauth_clients (
    id integer NOT NULL,
    user_id integer NOT NULL,
    name character varying(200) NOT NULL,
    secret character varying(100),
    provider character varying(200),
    redirect character varying(255),
    personal_access_client boolean,
    password_client boolean,
    revoked boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.oauth_clients OWNER TO "user";

--
-- Name: oauth_clients_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.oauth_clients_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oauth_clients_id_seq OWNER TO "user";

--
-- Name: oauth_clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.oauth_clients_id_seq OWNED BY public.oauth_clients.id;


--
-- Name: oauth_refresh_tokens; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.oauth_refresh_tokens (
    id character varying(255) NOT NULL,
    access_token_id character varying(255) NOT NULL,
    revoked boolean DEFAULT false,
    expires_at timestamp without time zone
);


ALTER TABLE public.oauth_refresh_tokens OWNER TO "user";

--
-- Name: users; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username character varying(255) NOT NULL,
    first_name character varying(128) NOT NULL,
    last_name character varying(128) NOT NULL,
    token character varying(255),
    password character varying(255),
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.users OWNER TO "user";

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO "user";

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: oauth_auth_codes id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.oauth_auth_codes ALTER COLUMN id SET DEFAULT nextval('public.oauth_auth_codes_id_seq'::regclass);


--
-- Name: oauth_clients id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.oauth_clients ALTER COLUMN id SET DEFAULT nextval('public.oauth_clients_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: oauth_access_tokens; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.oauth_access_tokens (id, user_id, client_id, scopes, revoked, expires_at) FROM stdin;
\.
COPY public.oauth_access_tokens (id, user_id, client_id, scopes, revoked, expires_at) FROM '$$PATH$$/2991.dat';

--
-- Data for Name: oauth_auth_codes; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.oauth_auth_codes (id, user_id, client_id, scopes, revoked, expires_at) FROM stdin;
\.
COPY public.oauth_auth_codes (id, user_id, client_id, scopes, revoked, expires_at) FROM '$$PATH$$/2994.dat';

--
-- Data for Name: oauth_clients; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.oauth_clients (id, user_id, name, secret, provider, redirect, personal_access_client, password_client, revoked, created_at, updated_at) FROM stdin;
\.
COPY public.oauth_clients (id, user_id, name, secret, provider, redirect, personal_access_client, password_client, revoked, created_at, updated_at) FROM '$$PATH$$/2990.dat';

--
-- Data for Name: oauth_refresh_tokens; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.oauth_refresh_tokens (id, access_token_id, revoked, expires_at) FROM stdin;
\.
COPY public.oauth_refresh_tokens (id, access_token_id, revoked, expires_at) FROM '$$PATH$$/2992.dat';

--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.users (id, username, first_name, last_name, token, password, created_at, updated_at) FROM stdin;
\.
COPY public.users (id, username, first_name, last_name, token, password, created_at, updated_at) FROM '$$PATH$$/2988.dat';

--
-- Name: oauth_auth_codes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.oauth_auth_codes_id_seq', 1, false);


--
-- Name: oauth_clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.oauth_clients_id_seq', 2, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- Name: oauth_access_tokens oauth_access_tokens_pk; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.oauth_access_tokens
    ADD CONSTRAINT oauth_access_tokens_pk PRIMARY KEY (id);


--
-- Name: oauth_refresh_tokens oauth_refresh_tokens_pk; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.oauth_refresh_tokens
    ADD CONSTRAINT oauth_refresh_tokens_pk PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--