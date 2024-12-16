CREATE TABLE jailed(
    id int primary key auto_increment,
    ResidentID varchar(20),
    reason varchar(255),
    JailedBy int,
    JailedAt datetime default current_timestamp
);
