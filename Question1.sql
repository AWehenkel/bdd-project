/* Scripts SQL permettant la creation de la base de donnees. */

Create table if not exists plateforme(
	id_plateforme int primary key,
	nom varchar(20) not null,
	version int not null,
	bits int not null
	)ENGINE=INNODB;

create table if not exists console(
	id_plateforme int primary key,
	Foreign key (id_plateforme) references plateforme(id_plateforme)
)ENGINE=INNODB;

create table if not exists systeme(
	id_plateforme int primary key,
	Foreign key (id_plateforme) references plateforme(id_plateforme)
)ENGINE=INNODB;

create table if not exists emulateur(
	id_emulateur int primary key,
	nom varchar(20) not null,
	version int not null
)ENGINE=INNODB;

create table if not exists ami(
	id_ami int primary key,
	nom varchar(20) not null,
	prenom varchar(20) not null,
	n_tel varchar(9) not null
)ENGINE=INNODB;

create table if not exists jeu_video(
	id_jeu int primary key,
	style varchar(20) not null,
	note int not null
)ENGINE=INNODB;

create table if not exists exemplaire(
	id_jeu int not null,
	id_exemplaire int not null,
	id_plateforme int not null,
	primary key(id_jeu, id_exemplaire),
	Foreign key (id_jeu) references jeu_video(id_jeu),
	Foreign key (id_plateforme) references plateforme(id_plateforme)
)ENGINE=INNODB;

ALTER TABLE `exemplaire` ADD KEY `id_exemplaire` (`id_exemplaire`);

create table if not exists exemplaire_physique(
	id_jeu int not null,
	id_exemplaire int not null,
	etat int not null,
	emballage boolean not null,
	livret boolean not null,
	primary key(id_jeu, id_exemplaire),
	Foreign key (id_jeu) references jeu_video(id_jeu)
)ENGINE=INNODB;

create table if not exists exemplaire_virtuel(
	id_jeu int not null,
	id_exemplaire int not null,
	taille int not null,
	primary key(id_jeu, id_exemplaire),
	Foreign key (id_jeu) references jeu_video(id_jeu)
)ENGINE=INNODB;

create table if not exists plateforme_du_jeu(
	id_jeu int not null,
	id_plateforme int not null,
	primary key(id_jeu, id_plateforme),
	Foreign key (id_plateforme) references plateforme(id_plateforme),
	Foreign key (id_jeu) references jeu_video(id_jeu)
)ENGINE=INNODB;

create table if not exists peut_emuler(
	id_jeu int not null,
	id_exemplaire int not null,
	id_emulateur int not null,
	primary key(id_jeu, id_exemplaire, id_emulateur),
	Foreign key (id_jeu) references jeu_video(id_jeu),
	Foreign key (id_exemplaire) references exemplaire(id_exemplaire),
	Foreign key (id_emulateur) references emulateur(id_emulateur)
)ENGINE=INNODB;

create table if not exists emulateur_fonctionne_sur(
	id_plateforme int not null,
	id_emulateur int not null,
	primary key(id_emulateur, id_plateforme),
	Foreign key (id_plateforme) references plateforme(id_plateforme),
	Foreign key (id_emulateur) references emulateur(id_emulateur)
)ENGINE=INNODB;

create table if not exists emule(
	id_emulateur int not null,
	id_plateforme int not null,
	primary key(id_emulateur, id_plateforme),
	Foreign key (id_plateforme) references plateforme(id_plateforme),
	Foreign key (id_emulateur) references emulateur(id_emulateur)
)ENGINE=INNODB;

create table if not exists pret(
	id_ami int not null,
	id_jeu int not null,
	id_exemplaire int not null,
	date_emprunt varchar(10) not null,
	date_retour varchar(10) null,
	primary key(id_jeu, id_exemplaire, date_emprunt),
	Foreign key (id_jeu) references jeu_video(id_jeu),
	Foreign key (id_exemplaire) references exemplaire(id_exemplaire)
)ENGINE=INNODB;

create table if not exists users(
	id varchar(20) NOT NULL,
	password varchar(20) NOT NULL
)ENGINE=INNODB;


/* Scripts SQL permettant d'initialiser la base de donnees. */

INSERT INTO users(id, password) VALUES ('antoine', 'password');
INSERT INTO users(id, password) VALUES ('group8', 'IR5ovtPs');

/* En ce qui concerne l'importation du fichier data.csv: voir rapport. */
