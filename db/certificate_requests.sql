CREATE TABLE certificate_requests(
    RequestNo int primary key auto_increment,
    ResidentNo varchar(255),
    ID varchar(255),
    RequestDate datetime default current_timestamp,
    HouseOwnerApproval enum("0","1") default "0",
    HouseOwnerApprovedAt datetime default null,
    VillageLeaderApproval enum("0","1") default "0",
    VillageLeaderApprovedAt datetime default null,
    CellLeaderApproval enum("0","1") default "0",
    CellLeaderApprovedAt datetime default null
);