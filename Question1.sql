
Create table if not exists Plateforme(
	id_plateforme int primary key,
	nom varchar(20) not null,
	version int not null,
	bits int not null
	)ENGINE=INNODB;
	
create table if not exists Console(
	id_plateforme int primary key,
	Foreign key (id_plateforme) references Plateforme(id_plateforme)
)ENGINE=INNODB;

create table if not exists Systeme(
	id_plateforme int primary key,
	Foreign key (id_plateforme) references Plateforme(id_plateforme)
)ENGINE=INNODB;

create table if not exists Emulateur(
	id_emulateur int primary key,
	nom varchar(20) not null,
	version int not null
)ENGINE=INNODB;

create table if not exists Ami(
	id_ami int primary key,
	nom varchar(20) not null,
	prenom varchar(20) not null,
	n_tel varchar(9) not null
)ENGINE=INNODB;

create table if not exists Jeu_Video(
	id_jeu int primary key,
	style varchar(20) not null,
	note int not null
)ENGINE=INNODB;

create table if not exists Exemplaire(
	id_jeu int not null,
	id_exemplaire int not null,
	id_plateforme int not null,
	primary key(id_jeu, id_exemplaire),
	Foreign key (id_jeu) references Jeu_Video(id_jeu),
	Foreign key (id_plateforme) references Plateforme(id_plateforme)
)ENGINE=INNODB;

create table if not exists Exemplaire_Physique(
	id_jeu int not null,
	id_exemplaire int not null,
	etat int not null,
	emballage boolean not null,
	livret boolean not null,
	primary key(id_jeu, id_exemplaire),
	Foreign key (id_jeu) references Jeu_Video(id_jeu)
)ENGINE=INNODB;

create table if not exists Exemplaire_Virtuel(
	id_jeu int not null,
	id_exemplaire int not null,	
	taille int not null,
	primary key(id_jeu, id_exemplaire),
	Foreign key (id_jeu) references Jeu_Video(id_jeu)
)ENGINE=INNODB;

create table if not exists plateforme_du_jeu(
	id_jeu int not null,
	id_plateforme int not null,
	primary key(id_jeu, id_plateforme),
	Foreign key (id_plateforme) references Plateforme(id_plateforme),
	Foreign key (id_jeu) references Jeu_Video(id_jeu)
)ENGINE=INNODB;

create table if not exists Peut_Emuler(
	id_jeu int not null,
	id_exemplaire int not null,
	id_emulateur int not null,
	primary key(id_jeu, id_exemplaire, id_emulateur),
	Foreign key (id_jeu) references Jeu_Video(id_jeu),
	Foreign key (id_exemplaire) references Exemplaire(id_exemplaire),
	Foreign key (id_emulateur) references Emulateur(id_emulateur)		
)ENGINE=INNODB;

create table if not exists Emulateur_Fonctionne_Sur(
	id_plateforme int not null,
	id_emulateur int not null,
	primary key(id_emulateur, id_plateforme),
	Foreign key (id_plateforme) references Plateforme(id_plateforme),
	Foreign key (id_emulateur) references Emulateur(id_emulateur)		
)ENGINE=INNODB;

create table if not exists Emule(
	id_emulateur int not null,
	id_plateforme int not null,
	primary key(id_emulateur, id_plateforme),
	Foreign key (id_plateforme) references Plateforme(id_plateforme),
	Foreign key (id_emulateur) references Emulateur(id_emulateur)		
)ENGINE=INNODB;

create table if not exists Pret(
	id_ami int not null,
	id_jeu int not null,
	id_exemplaire int not null,
	date_emprunt date not null,
	date_retour date not null,
	primary key(id_jeu, id_exemplaire, date_emprunt),
	Foreign key (id_jeu) references Jeu_Video(id_jeu),
	Foreign key (id_exemplaire) references Exemplaire(id_exemplaire)	
)ENGINE=INNODB;

LOAD DATA LOCAL INFILE 'C:\plateforme.csv'
INTO TABLE Plateforme
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_plateforme, nom, version, bits);

LOAD DATA LOCAL INFILE 'C:\Console.csv'
INTO TABLE Console
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_plateforme);

LOAD DATA LOCAL INFILE 'C:\Systeme.csv'
INTO TABLE Systeme
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_plateforme);

LOAD DATA LOCAL INFILE 'C:\Emulateur.csv'
INTO TABLE Emulateur
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_emulateur, nom, version);

LOAD DATA LOCAL INFILE 'C:\Ami.csv'
INTO TABLE Ami
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_ami, nom, prenom, n_tel);

LOAD DATA LOCAL INFILE 'C:\Jeu_Video.csv'
INTO TABLE Jeu_Video
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_jeu, style, note);

LOAD DATA LOCAL INFILE 'C:\Exemplaire.csv'
INTO TABLE Exemplaire
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_jeu, id_exemplaire, id_plateforme);

LOAD DATA LOCAL INFILE 'C:\Exemplaire_Physique.csv'
INTO TABLE Exemplaire_Physique
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_jeu, id_exemplaire, etat, emballage, livret);

LOAD DATA LOCAL INFILE 'C:\Exemplaire_Virtuel.csv'
INTO TABLE Exemplaire_Virtuel
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_jeu, id_exemplaire, taille);

LOAD DATA LOCAL INFILE 'C:\Plateforme_Jeu.csv'
INTO TABLE plateforme_du_jeu
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_jeu, id_plateforme);

LOAD DATA LOCAL INFILE 'C:\Peut_Emuler.csv'
INTO TABLE Peut_Emuler
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_jeu, id_exemplaire, id_emulateur);

LOAD DATA LOCAL INFILE 'C:\Emulateur_Fonctionne_Sur.csv'
INTO TABLE Emulateur_Fonctionne_Sur
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_plateforme, id_emulateur);

LOAD DATA LOCAL INFILE 'C:\Emule.csv'
INTO TABLE Emule
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_emulateur, id_plateforme);

LOAD DATA LOCAL INFILE 'C:\Pret.csv'
INTO TABLE Pret
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
(id_ami, id_jeu, id_exemplaire, date_emprunt, date_retour);
