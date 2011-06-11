create table products_price_history 
( 
  id int(11) not null, 
  upc bigint(13) unsigned zerofill not null, 
  updated timestamp default now(), 
  normal_cost double default '0.00', 
  primary key (id,upc) 
) ENGINE=MyISAM DEFAULT CHARSET=latin1; 

create table brands 
(
    brandid int(11) not null auto_increment,
    brand varchar(30) not null,
    primary key (brandid),
 unique index(brand)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
