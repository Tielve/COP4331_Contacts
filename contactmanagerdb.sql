create database contactmanagerdb;
use contactmanagerdb;

create table users(
	id integer not null unique auto_increment,
    primary key (id),
    
    username varchar(50) not null unique,
    pw varchar(50) not null
    
);

create table contacts(
	cID integer not null unique auto_increment,
    primary key (cID),
    
    fname varchar(50) not null,
    lname varchar(50) not null,
    phone char(14),
    email varchar(50),
    company varchar(50),
    
    check (phone like "(___)-___-____"),
    
    uID integer not null,
    foreign key (uID) references users(id)
);