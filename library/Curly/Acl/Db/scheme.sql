drop table if exists AclRules;
drop table if exists AclRoleParents;
drop table if exists AclResources;
drop table if exists AclRoles;

create table AclRoles (
	id			varchar(32) not null,
	primary key(id)
) ENGINE=InnoDB;

create table AclResources (
	id			varchar(32) not null,
	parent		varchar(32),
	primary key(id),
	foreign key(parent)
		references AclResources(id)
			on delete cascade
			on update cascade
) ENGINE=InnoDB;

create table AclRoleParents (
	parent		varchar(32) not null,
	child		varchar(32) not null,
	foreign key(parent)
		references AclRoles(id)
			on delete cascade
			on update cascade,
	foreign key(child)
		references AclRoles(id)
			on delete cascade
			on update cascade,
	primary key(parent, child)
) ENGINE=InnoDB;

create table AclRules (
	privilege	varchar(32),
	role		varchar(32),
	resource	varchar(32),
	allow		bool default false not null,
	foreign key(role)
		references AclRoles(id)
			on delete cascade
			on update cascade,
	foreign key(resource)
		references AclResources(id)
			on delete cascade
			on update cascade,
	unique(privilege, role, resource)
) ENGINE=InnoDB;
