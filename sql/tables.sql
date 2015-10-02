
# db fedweb


drop table resources IF EXISTS;
create table resources(
  id int auto_increment primary key,
  affiliation  char(100),
  cc           char(10),
  contact      char(100),
  deputies     char(100),
  hostsite     char(100),
  cmf          char(100),
  ep_occi      char(100),
  ep_cdmi      char(100),
  res_size     text,
  vm_max_size  text
);

drop table ostpl IF EXISTS;
create table ostpl (
  id int auto_increment primary key,
  resid        int comment "resources.id",
  uri          char(255) comment "eg http://occi.example.com/path/to/os_tpl#uuid_centos_6_6",
  osid         char(100) comment "uuid_centos_6_6",
  idate        datetime
);

