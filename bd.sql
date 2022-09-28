BEGIN;

CREATE TABLE public.usuarios(
login text primary key,
token text not null
);

CREATE TABLE public.products(
pid serial primary key,
name text not null,
price decimal(10,2) not null,
description text not null,
img text not null,
usuarios_login text not null,
created_at timestamp default now()
);

ALTER TABLE public.products
    ADD FOREIGN KEY (usuarios_login)
    REFERENCES public.usuarios (login)
    NOT VALID;
	
END;