create table users(
	id int primary key auto_increment not null,
	username varchar(100) not null,
	password varchar(255) not null,
	name varchar(100) not null,
	email varchar(60) not null,
	created datetime not null,
	modified datetime not null,
	role varchar(20) not null
);

create table receipts(
	id int primary key auto_increment not null,
	payment date not null,
	send date not null,
	user_id int not null,
	aproved boolean not null
);

create table files(
	id int primary key auto_increment not null,
	name varchar(255) not null,
	src varchar(255) not null,
	receipt_id int not null
);


