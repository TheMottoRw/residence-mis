-- SCRIPT CHANGES

alter table certificate_requests change ID ID bigint;
alter table citizen_abroad change ResidentID ResidentID bigint;
alter table citizen_abroad change AddedBy AddedBy bigint;
alter table jailed change ResidentID ResidentID bigint;
alter table jailed change JailedBy JailedBy bigint;

-- Add foreign key in Houses table for ID referencing Users(ID)
ALTER TABLE houses
ADD CONSTRAINT FK_houses_users FOREIGN KEY (ID) REFERENCES users(ID);

-- Add foreign key in Resident table for HouseNo referencing houses(HouseNo)
ALTER TABLE resident
ADD CONSTRAINT FK_Resident_Houses FOREIGN KEY (HouseNo) REFERENCES houses(HouseNo);


-- Add foreign key in Certificate_Requests table for ResidentNo referencing resident(ID)
ALTER TABLE certificate_requests
ADD CONSTRAINT FK_CertificateRequests_Resident FOREIGN KEY (ID) REFERENCES resident(ID);

-- Add foreign keys in Jailed table for ResidentID referencing Resident(ID) and JailedBy referencing users(ID)
ALTER TABLE jailed
ADD CONSTRAINT FK_Jailed_Resident FOREIGN KEY (ResidentID) REFERENCES resident(ID),
 ADD  CONSTRAINT FK_Jailed_Users FOREIGN KEY (JailedBy) REFERENCES users(ID);

-- Add foreign keys in Citizen_Abroad table for ResidentID referencing Resident(ID) and AddedBy referencing users(ID)
ALTER TABLE citizen_abroad
ADD CONSTRAINT FK_CitizenAbroad_Resident FOREIGN KEY (ResidentID) REFERENCES resident(ID),
ADD    CONSTRAINT FK_CitizenAbroad_Users FOREIGN KEY (AddedBy) REFERENCES users(ID);
