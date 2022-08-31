USE coolbrew;

DROP TABLE IF EXISTS pr_product;
CREATE TABLE pr_product (
  ProductID int(11) unsigned NOT NULL auto_increment,
  UPC varchar(11) default NULL,
  SiteID varchar(20) NOT NULL default '',
  FilterID int(11) default NULL,
  Status varchar(20) default NULL,
  Verified varchar(127) default NULL,
  ProductName varchar(255) default NULL,
  LongDescription text,
  Teaser varchar(255) default NULL,
  Benefits text,
  AvailableIn varchar(255) default NULL,
  Footnotes text,
  Ingredients text,
  NutritionBlend text,
  Standardization varchar(255) default NULL,
  Directions text,
  Warning text,
  AllNatural text,
  Gluten varchar(127) default NULL,
  OrganicStatement text,
  ThumbFile varchar(127) default NULL,
  ThumbWidth int(11) unsigned default NULL,
  ThumbHeight int(11) unsigned default NULL,
  ThumbAlt varchar(127) default NULL,
  SmallFile varchar(127) default NULL,
  SmallWidth int(11) unsigned default NULL,
  SmallHeight int(11) unsigned default NULL,
  SmallAlt varchar(127) default NULL,
  LargeFile varchar(127) default NULL,
  LargeWidth int(11) unsigned default NULL,
  LargeHeight int(11) unsigned default NULL,
  LargeAlt varchar(127) default NULL,
  NutritionFacts varchar(255) default NULL,
  KosherSymbol int(11) default NULL,
  OrganicSymbol int(11) default NULL,
  CaffeineFile varchar(127) default NULL,
  CaffeineWidth int(11) unsigned default NULL,
  CaffeineHeight int(11) unsigned default NULL,
  CaffeineAlt varchar(127) default NULL,
  StoreSection int(11) default NULL,
  LocatorCode varchar(10) default NULL,
  MenuSubsection varchar(60) default NULL,
  DiscontinueDate date default NULL,
  Replacements text,
  MetaTitle text,
  LastModifiedDate date default NULL,
  LastModifiedBy varchar(60) default NULL,
  MetaMisc text,
  MetaDescription text,
  MetaKeywords text,
  Components int(11) default NULL,
  ProductType varchar(20) default NULL,
  FlavorDescriptor text,
  SortOrder int(11) unsigned default NULL,
  FlagAsNew int(11) unsigned default NULL,
  Featured int(11) unsigned default NULL,
  SpiceLevel varchar(255) default NULL,
  Alergens text,
  FeatureFile varchar(127) default NULL,
  FeatureWidth int(11) unsigned default NULL,
  FeatureHeight int(11) unsigned default NULL,
  FeatureAlt varchar(127) default NULL,
  BeautyFile varchar(127) default NULL,
  BeautyWidth int(11) unsigned default NULL,
  BeautyHeight int(11) unsigned default NULL,
  BeautyAlt varchar(127) default NULL,
  PackageSize varchar(127) default NULL,
  ProductGroup varchar(127) default NULL,
  Language varchar(15) default NULL,
  SESFilename varchar(127) default NULL,
  PRIMARY KEY  (ProductID)
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_product.txt" INTO TABLE pr_product FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


# This table is now updated dynamically. Do not overwrite.
# 
# DROP TABLE IF EXISTS pr_product_category;
# CREATE TABLE pr_product_category (
#   ProductID int(11) unsigned NOT NULL default '0',
#   CategoryID int(11) NOT NULL default '0'
# );
# 
# 
# LOAD DATA INFILE "/var/opt/httpd/data/pr_product_category.txt" INTO TABLE 
# pr_product_category FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS pr_category;
CREATE TABLE pr_category (
  CategoryID int(11) unsigned NOT NULL auto_increment,
  SiteID char(20) NOT NULL default '',
  CategoryCode varchar(255) default NULL,
  CategoryName varchar(255) default NULL,
  CategoryDescription text,
  CategoryType varchar(32) default NULL,
  Status int(11) NOT NULL default '0',
  CategoryParentID int(11) default NULL,
  CategoryOrder int(11) default NULL,
  CategoryText text,
  SESFilename varchar(127) default NULL,
  Language varchar(5) default NULL,
  MetaTitle text,
  MetaMisc text,
  MetaDescription text,
  MetaKeywords text,
  Lft int(11) unsigned default NULL,
  Rgt int(11) unsigned default NULL,
  PRIMARY KEY  (CategoryID)
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_category.txt" INTO TABLE pr_category FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS site;
CREATE TABLE site (
  SiteID varchar(20) NOT NULL default '',
  BrandName char(128) NOT NULL default '',
  BaseURL char(255) NOT NULL default '',
  BasePath char(255) NOT NULL default '',
  StoreID char(128) default NULL,
  PRIMARY KEY  (SiteID)
);

LOAD DATA INFILE "/var/opt/httpd/data/site.txt" INTO TABLE site FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS pr_symbol;
CREATE TABLE pr_symbol (
  SymbolID int(11) unsigned NOT NULL auto_increment,
  SymbolFile char(255) NOT NULL default '',
  SymbolWidth int(11) unsigned default NULL,
  SymbolHeight int(11) unsigned default NULL,
  SymbolAlt char(255) default NULL,
  PRIMARY KEY  (SymbolID)
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_symbol.txt" INTO TABLE pr_symbol FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS pr_nlea;
CREATE TABLE pr_nlea (
  ProductID int(11) unsigned primary key,
  SiteID char(2) not null,
  ProductName char(255),
  ProductFile char(255),
  TYPE int(11) unsigned,
  SSIZE char(255),
  MAKE char(255),
  SERV char(255),
  CAL char(8),
  2CAL char(8),
  FATCAL char(8),
  2FATCAL char(8),
  TFATQ char(8),
  TFATP char(8),
  2TFATP char(8),
  SFATQ char(8),
  SFATP char(8),
  2SFATP char(8),
  PFATQ char(8),
  MFATQ char(8),
  CHOLQ char(8),
  CHOLP char(8),
  2CHOLP char(8),
  SODQ char(8),
  SODP char(8),
  2SODP char(8),
  POTQ char(8),
  POTP char(8),
  2POTP char(8),
  TCARBQ char(8),
  TCARBP char(8),
  2TCARBP char(8),
  DFIBQ char(8),
  DFIBP char(8),
  2DFIBP char(8),
  SFIBQ char(8),
  IFIBQ char(8),
  SUGQ char(8),
  PROTQ char(8),
  PROTP char(8),
  2PROTP char(8),
  VITAP char(8),
  2VITAP char(8),
  VITCQ char(8),
  VITCP char(8),
  2VITCP char(8),
  CALCP char(8),
  2CALCP char(8),
  IRONP char(8),
  2IRONP char(8),
  VITDP char(8),
  2VITDP char(8),
  VITB6P char(8),
  2VITB6P char(8),
  FOLATEP char(8),
  2FOLATEP char(8),
  VITB12P char(8),
  2VITB12P char(8),
  VITEP char(8),
  2VITEP char(8),
  THIAP char(8),
  2THIAP char(8),
  RIBOP char(8),
  2RIBOP char(8),
  PHOSP char(8),
  2PHOSP char(8),
  MAGNP char(8),
  2MAGNP char(8),
  NIACP char(8),
  2NIACP char(8),
  ZINCP char(8),
  2ZINCP char(8),
  STMT1 char(3),
  STMT1Q char(12),
  PDV1 char(3),
  PDV2 char(3),
  PDVT char(3),
  OCARBQ char(8),
  sort char(8),
  FOLICP char(8),
  2FOLICP char(8),
  CHLORP char(8),
  2CHLORP char(8),
  BIOTINP char(8),
  2BIOTINP char(8),
  PACIDP char(8),
  2PACIDP char(8),
  IODIP char(8),
  2IODIP char(8),
  SELEP char(8),
  2SELEP char(8),
  COPPP char(8),
  2COPPP char(8),
  MANGP char(8),
  2MANGP char(8),
  CHROMP char(8),
  2CHROMP char(8),
  MOLYP char(8),
  2MOLYP char(8),
  VITKP char(8),
  2VITKP char(8),
  HFATQ char(8),
  STMT2Q char(255),
  STMT2 char(3),
  TFATQ2 char(8),
  SFATQ2 char(8),
  PFATQ2 char(8),
  MFATQ2 char(8),
  HFATQ2 char(8),
  CHOLQ2 char(8),
  SODQ2 char(8),
  POTQ2 char(8),
  TCARBQ2 char(8),
  DFIBQ2 char(8),
  SFIBQ2 char(8),
  IFIBQ2 char(8),
  SUGQ2 char(8),
  OCARBQ2 char(8),
  PROTQ2 char(8),
  COL1HD char(24),
  COL2HD char(24)
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_nlea.txt" INTO TABLE pr_nlea FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS pr_ingredient;
CREATE TABLE pr_ingredient (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `SiteID` varchar(20) NOT NULL default '',
  `Ingredient` varchar(255) NOT NULL default '',
  `LatinName` varchar(255) default NULL,
  `ImageFile` varchar(127) default NULL,
  `ImageWidth` int(11) unsigned default NULL,
  `ImageHeight` int(11) unsigned default NULL,
  `ImageAlt` varchar(127) default NULL,
  `Status` varchar(20) NOT NULL default 'in use',
  `Description` text,
  `CreatedDate` datetime default NULL,
  `CreatedBy` varchar(16) NOT NULL default '',
  `RevisedDate` datetime default NULL,
  `RevisedBy` varchar(16) NOT NULL default '',
  PRIMARY KEY  (ID)
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_ingredient.txt" INTO TABLE pr_ingredient FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


DROP TABLE IF EXISTS pr_ingredient_link;
CREATE TABLE pr_ingredient_link (
  `IngredientID` int(11) unsigned NOT NULL,
  `Ingredient` varchar(255) NOT NULL default ''
);

LOAD DATA INFILE "/var/opt/httpd/data/pr_ingredient_link.txt" INTO TABLE pr_ingredient_link FIELDS TERMINATED BY '\t' ENCLOSED BY '' ESCAPED BY '\\';


