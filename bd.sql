CREATE TABLE products(
pid serial primary key,
name varchar(150) not null,
price decimal(10,2) not null,
description text not null,
img text not null,
created_at timestamp default now()
);