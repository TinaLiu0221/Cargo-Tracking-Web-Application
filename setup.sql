drop table ItemToCustomer;
drop table ItemToStore;
drop table Customer;
drop table Item;
drop table ShippingStatus;

CREATE TABLE Customer (customerID int not null, name varchar(15) not null, age int not null, gender varchar(6) not null, primary key (customerID));

grant select on Customer to public;

CREATE TABLE Item (itemID int not null, price float not null, primary key (itemID));

grant select on Item to public;

CREATE TABLE ItemToCustomer (itemID int not null, customerID int not null, primary key (itemID, customerID));

grant select on ItemToCustomer to public;

CREATE TABLE ItemToStore (itemID int not null, storeID int not null, primary key (itemID));

grant select on ItemToStore to public;

CREATE TABLE ShippingStatus (itemID int not null,  currentLocation varchar(30) null, primary key (itemID));

grant select on ShippingStatus to public;

insert into Customer values (1, 'Peter', 15, 'male');
insert into Customer values (2, 'Jason', 20, 'male');
insert into Customer values (3, 'Tim', 25, 'male');
insert into Customer values (4, 'Mia', 15, 'female');
insert into Customer values (5, 'Alice', 20, 'female');
insert into Customer values (6, 'Zoe', 25, 'female');

insert into Item values (11, 10);
insert into Item values (12, 15);
insert into Item values (13, 20);
insert into Item values (14, 25);
insert into Item values (15, 30);
insert into Item values (21, 35);
insert into Item values (22, 40);
insert into Item values (23, 45);
insert into Item values (24, 50);
insert into Item values (25, 55);
insert into Item values (31, 60);
insert into Item values (32, 65);
insert into Item values (33, 70);
insert into Item values (34, 75);
insert into Item values (35, 80);

insert into ItemToCustomer values (11, 1);
insert into ItemToCustomer values (12, 2);
insert into ItemToCustomer values (13, 3);
insert into ItemToCustomer values (14, 4);
insert into ItemToCustomer values (15, 5);
insert into ItemToCustomer values (21, 1);
insert into ItemToCustomer values (22, 2);
insert into ItemToCustomer values (23, 3);
insert into ItemToCustomer values (24, 4);
insert into ItemToCustomer values (25, 5);
insert into ItemToCustomer values (31, 1);
insert into ItemToCustomer values (32, 2);
insert into ItemToCustomer values (33, 3);
insert into ItemToCustomer values (34, 4);
insert into ItemToCustomer values (35, 5);

insert into ItemToStore values (11, 1);
insert into ItemToStore values (12, 1);
insert into ItemToStore values (13, 1);
insert into ItemToStore values (14, 1);
insert into ItemToStore values (15, 1);
insert into ItemToStore values (21, 2);
insert into ItemToStore values (22, 2);
insert into ItemToStore values (23, 2);
insert into ItemToStore values (24, 2);
insert into ItemToStore values (25, 2);
insert into ItemToStore values (31, 3);
insert into ItemToStore values (32, 3);
insert into ItemToStore values (33, 3);
insert into ItemToStore values (34, 3);
insert into ItemToStore values (35, 3);

insert into ShippingStatus values (11, 'Vancouver');
insert into ShippingStatus values (12, 'Vancouver');
insert into ShippingStatus values (13, 'Vancouver');
insert into ShippingStatus values (14, 'Vancouver');
insert into ShippingStatus values (15, 'Vancouver');
insert into ShippingStatus values (21, 'Vancouver');
insert into ShippingStatus values (22, 'Richmond');
insert into ShippingStatus values (23, 'Richmond');
insert into ShippingStatus values (24, 'Richmond');
insert into ShippingStatus values (25, 'Richmond');
insert into ShippingStatus values (31, 'Richmond');
insert into ShippingStatus values (32, 'Victoria');
insert into ShippingStatus values (33, 'Victoria');
insert into ShippingStatus values (34, 'Victoria');
insert into ShippingStatus values (35, 'Victoria');