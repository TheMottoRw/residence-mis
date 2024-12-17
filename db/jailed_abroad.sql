CREATE TABLE jailed(
    id int primary key auto_increment,
    ResidentID varchar(20),
    reason varchar(255),
    JailedBy int,
    JailedAt datetime default current_timestamp
);

CREATE TABLE citizen_abroad(
    id int primary key auto_increment,
    ResidentID varchar(20),
    Country varchar(255),
    City varchar(255),
    State varchar(255),
    status enum("Abroad","Incountry"),
    AddedBy varchar(255),
    AddedAt datetime default current_timestamp
);
