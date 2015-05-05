<?
//结合slow.log 和 mysqlsla 分析软件 对没有添加索引的语句进行分析
//此次优化，主要根据查询热度和join query 做的索引
//
1、[parse sql]
	SELECT `CRM_userRolePermissions`.*, `CRM_userPermissions`.* FROM `CRM_userRolePermissions` INNER JOIN `CRM_userPermissions` ON `CRM_userPermissions`.`id` = `CRM_userRolePermissions`.`CRM_userPermissions_id` WHERE CRM_userRoles_id = 112
   
   [add index] 
   	   field name 
		CRM_userRolePermissions.CRM_userPermissions_id [index](necessary)
		CRM_userRolePermissions.CRM_userRoles_id [index]
   [parse result]
   		before：scanning rows 2451
   		after：scanning rows 2


3、[parse sql ]

	SELECT `LCDScreen`.* FROM `LCDScreen` WHERE `location_id` = '286' AND `CRM_userCompanyDetails_id` = '127';
	SELECT `lcdInfo`.*, `slocation`.`buildingName` AS `buildingName`, `slocation`.`streetDetails` AS `streetDetails`, `slocation`.`district_id` AS `district_id`, `slocation`.`GPSlocation` AS `GPSlocation`, `com`.`companyName` AS `companyName`, `district`.`id` AS `districtID`, `district`.`description` AS `district`, `city`.`id` AS `cityID`, `city`.`city` AS `city`, `pro`.`id` AS `proID`, `pro`.`province` AS `province` FROM `LCDScreen` AS `lcdInfo` INNER JOIN `locations` AS `slocation` ON `lcdInfo`.`location_id` = `slocation`.`id` LEFT JOIN `CRM_userCompanyDetails` AS `com` ON `lcdInfo`.`CRM_userCompanyDetails_id` = `com`.`id` LEFT JOIN `districtTable` AS `district` ON `slocation`.`district_id` = `district`.`id` LEFT JOIN `cityTable` AS `city` ON `district`.`cityTable_id` = `city`.`id` LEFT JOIN `provinceTable` AS `pro` ON `city`.`provinceTable_id` = `pro`.`id` WHERE `lcdInfo`.`CRM_userCompanyDetails_id` = '206' ORDER BY `lcdInfo`.`id` DESC;
   SELECT `lt`.*, `ct`.`city` AS `city`, `pt`.`province` AS `province`, `dt`.`description` AS `district`, `lc`.`description` AS `locationCategory`, `lsc`.`description` AS `childLocationCategory`, count(ls.location_id) AS `ScreenInstalled`, `bc`.`CRM_userCompanyDetails_id` AS `CRM_userCompanyDetails_id` FROM `locations` AS `lt` LEFT JOIN `cityTable` AS `ct` ON `lt`.`cityTable_id` = `ct`.`id` LEFT JOIN `provinceTable` AS `pt` ON `ct`.`provinceTable_id` = `pt`.`id` LEFT JOIN `districtTable` AS `dt` ON `lt`.`district_id` = `dt`.`id` LEFT JOIN `locationCategories` AS `lc` ON `lt`.`locationCategories_id` = `lc`.`id` LEFT JOIN `locationSubCategories` AS `lsc` ON `lt`.`locationSubCategories_id` =`lsc`.`id` LEFT JOIN `LCDScreen` AS `ls` ON `lt`.`id` = `ls`.`location_id` RIGHT JOIN `locationBodyCorporate` AS `bc` ON `bc`.`id` = `lt`.`locationBodyCorporate_id` WHERE bc.CRM_userCompanyDetails_id = 344 and bc.id = lt.locationBodyCorporate_id GROUP BY `lt`.`id`, `ls`.`location_id` ORDER BY `lt`.`id` DESC;
   SELECT `lt`.*, `ct`.`city` AS `city`, `pt`.`province` AS `province`, `dt`.`description` AS `district`, `lc`.`description` AS `locationCategory`, `lsc`.`description` AS `childLocationCategory`, count(ls.location_id) AS `ScreenInstalled` FROM `locations` AS `lt` LEFT JOIN `cityTable` AS `ct` ON `lt`.`cityTable_id` = `ct`.`id` LEFT JOIN `provinceTable` AS `pt` ON `ct`.`provinceTable_id` = `pt`.`id` LEFT JOIN `districtTable` AS `dt` ON `lt`.`district_id` = `dt`.`id` LEFT JOIN `locationCategories` AS `lc` ON `lt`.`locationCategories_id` = `lc`.`id` LEFT JOIN `locationSubCategories` AS `lsc` ON `lt`.`locationSubCategories_id` =`lsc`.`id` LEFT JOIN `LCDScreen` AS `ls` ON `lt`.`id` = `ls`.`location_id` WHERE `lt`.`CRM_userCompanyDetails_id` = '282' GROUP BY `lt`.`id`, `ls`.`location_id` ORDER BY `lt`.`id` DESC;
 
    [add index] 
   	   field name
		LCDScreen.location_id [index](necessary)
		LCDScreen.CRM_userCompanyDetails_id [index](necessary)

   [parse result]
   		before：scanning rows 190
   		after：scanning rows 3

4、[parse sql]

   SELECT `lts`.`timeCode` AS `timeCode`, `lts`.`startTime` AS `startTime` FROM `LCDScreenTimes` AS `lst` LEFT JOIN `LCDScreen` AS `ls` ON `ls`.`id`=`lst`.`LCDScreen_id` LEFT JOIN `LCDTimes` AS `lt` ON `lt`.`id`=`lst`.`LCDTimes_id` LEFT JOIN `LCDTimesSaved` AS `lts` ON `lts`.`LCDTimes_id`=`lst`.`LCDTimes_id` WHERE `ls`.`id` = '315' AND `lts`.`day` = '1';
	   SELECT * FROM `LCDScreenTimes` ORDER BY `LCDScreen_id` DESC LIMIT 0,1000;
	   SELECT * FROM `LCDScreenTimes` LIMIT 0,1000;
	   SELECT COUNT(1) FROM `LCDScreenTimes`;
	   SELECT * FROM `LCDScreenTimes` ORDER BY `LCDScreen_id` LIMIT 0,1000;
	   SELECT * FROM `LCDScreenTimes` WHERE `LCDScreen_id` = '12' ORDER BY `LCDScreen_id` DESC LIMIT 0,1000;
      explain SELECT `sts`.* FROM `LCDTimesSaved` AS `sts` WHERE `sts`.`LCDtimes_id` = '395';
      explain SELECT `main`.`day` AS `day`, `main`.`startTime` AS `startTime`, `main`.`LCDTimes_id` AS `LCDTimes_id`, `lt`.`description` AS `ltdescription` FROM `LCDTimesSaved` AS `main` LEFT JOIN `LCDTimes` AS `lt` ON `lt`.`id`=`main`.`LCDTimes_id` WHERE `lt`.`id` = '393' GROUP BY `day`;

   [add index] 
   	   field name 
		LCDTimesSaved.LCDTimes_id[index] (necessary)
		LCDScreenTimes.LCDScreen_id[index] (necessary)
   [parse result]
   		before：scanning rows 10756
   		after：scanning rows 27

5、[parse sql]

   SELECT `main`.*, `cp`.`id` AS `cp_id`, `cp`.`title` AS `cp_title`, `cp`.`offPeakPPD` AS `cp_offPeakPPD`, `cp`.`peakPPD` AS `cp_peakPPD`, `cp`.`runningDays` AS `cp_runningDays`, `cp`.`description` AS `cp_description`, `cp`.`CRM_userTable_id` AS `cp_CRM_userTable_id`, `cp`.`CRM_userCompanyDetails_id` AS `cp_CRM_userCompanyDetails_id`, `cp`.`selectedAudience` AS `cp_selectedAudience`, `cp`.`startDate` AS `cp_startDate`, `cp`.`endDate` AS `cp_endDate`, `cp`.`playlistDuration` AS `cp_playlistDuration`, `ut`.`CRM_userCompanyDetails_id` AS `ut_CRM_userCompanyDetails_id`, `ucd`.`companyName` AS `ucd_companyName`, `ucd`.`id` AS `ucd_companyNameid`, `cucd`.`companyName` AS `cucd_companyNameClient`, `ssm`.`title` AS `ssm_title`, `csm`.`id` AS `csmid` FROM `report_campaignHistory` AS `main` LEFT JOIN `CRM_campaignProposed` AS `cp` ON `cp`.`id`=`main`.`campaignProposed_id` LEFT JOIN `CRM_userTable` AS `ut` ON `ut`.`id`=`cp`.`CRM_userTable_id` LEFT JOIN `CRM_userCompanyDetails` AS `ucd` ON `ucd`.`id`=`ut`.`CRM_userCompanyDetails_id` LEFT JOIN `CRM_userCompanyDetails` AS `cucd` ON `cucd`.`id`=`cp`.`CRM_userCompanyDetails_id` LEFT JOIN `sponsorMedia` AS `ssm` ON `ssm`.`id`=`main`.`sponsorMedia_id` LEFT JOIN `CRM_campaignSelectedMedia` AS `csm` ON `csm`.`CRM_campaignProposed_id`=`main`.`campaignProposed_id` WHERE `main`.`dateTime` >= '2015-02-02 00:00:00' AND `main`.`dateTime` <= '2015-02-02 23:59:59' AND `main`.`LCDScreen_id` = '2' GROUP BY `main`.`id` ORDER BY `main`.`dateTime` ASC;

   [add index] 
   	   field name 
		report_campaignHistory.LCDScreen_id[index] (necessary)
		CRM_campaignSelectedMedia.CRM_campaignProposed_id[index] (necessary)
   [parse result]
   		before：scanning rows 920
   		after：scanning rows 7

6、[parse sql]

   SELECT `vcsi`.`buildingName` AS `buildingName`, `vcsi`.`streetDetails` AS `streetDetails`, `vcsi`.`installationDescription` AS `installationDescription`, `ls`.`serialNumber` AS `serialNumber`, `ct`.`city` AS `city`, `pt`.`province` AS `province`, `lsc`.`description` AS `subcategory` FROM `vCacheScreenInformation` AS `vcsi` LEFT JOIN `LCDScreen` AS `ls` ON `vcsi`.`LCDScreen_id` = `ls`.`id` LEFT JOIN `cityTable` AS `ct` ON `ct`.`id`=`vcsi`.`cityTable_id` LEFT JOIN `provinceTable` AS `pt` ON `pt`.`id`=`ct`.`provinceTable_id` LEFT JOIN `locationSubCategories` AS `lsc` ON `lsc`.`id` = `vcsi`.`locationSubCategories_id` WHERE `vcsi`.`LCDScreen_id` = '19' LIMIT 1;

   [add index] 
   	   field name 
		CRM_CFDValues.cityTable_id[index] (necessary)
		CRM_MCbasePrice.cityTable_id[index] (necessary)
   [parse result]
   		before：scanning rows 469
   		after：scanning rows 16
7、[parse sql]

   SELECT `vcsi`.`buildingName` AS `buildingName`, `vcsi`.`streetDetails` AS `streetDetails`, `vcsi`.`installationDescription` AS `installationDescription`, `ls`.`serialNumber` AS `serialNumber`, `ct`.`city` AS `city`, `pt`.`province` AS `province`, `lsc`.`description` AS `subcategory` FROM `vCacheScreenInformation` AS `vcsi` LEFT JOIN `LCDScreen` AS `ls` ON `vcsi`.`LCDScreen_id` = `ls`.`id` LEFT JOIN `cityTable` AS `ct` ON `ct`.`id`=`vcsi`.`cityTable_id` LEFT JOIN `provinceTable` AS `pt` ON `pt`.`id`=`ct`.`provinceTable_id` LEFT JOIN `locationSubCategories` AS `lsc` ON `lsc`.`id` = `vcsi`.`locationSubCategories_id` WHERE `vcsi`.`LCDScreen_id` = '19' LIMIT 1;
   SELECT `CRM_campaignSelectedScreens`.`LCDScreen_id` AS `LCDScreen_id`, `LCDScreen`.`serialNumber` AS `serialNumber` FROM `CRM_campaignSelectedScreens` INNER JOIN `LCDScreen` ON `LCDScreen`.`id` = `CRM_campaignSelectedScreens`.`LCDScreen_id` WHERE `CRM_campaignSelectedScreens`.`CRM_campaignProposed_id` = '72' GROUP BY `LCDScreen_id`;

   [add index] 
   	   field name 
		CRM_campaignSelectedScreens.LCDScreen_id[index](necessary)
      CRM_campaignSelectedScreens.CRM_campaignProposed_id[index](necessary)
   [parse result]
   		before：scanning rows 5469
   		after：scanning rows 196

8、[parse sql]

   SELECT `csi`.*, `city`.`lat` AS `lat`, `city`.`lng` AS `lng`, `lsc`.`description` AS `description` FROM `vCacheScreenInformation` AS `csi` LEFT JOIN `cityTable` AS `city` ON `city`.`id`=`csi`.`cityTable_id` LEFT JOIN `locationSubCategories` AS `lsc` ON `lsc`.`id`=`csi`.`locationSubCategories_id` LEFT JOIN `LCDScreenPricesAssigned` AS `lspa` ON `lspa`.`LCDScreen_id`=`csi`.`LCDScreen_id` LEFT JOIN `LCDScreenPrices` AS `lsp` ON `lsp`.`id`=`lspa`.`LCDScreenPrices_id` WHERE `csi`.`population` >= 0 AND `csi`.`population` <= '10000000' AND `csi`.`income` >= '10000' AND `csi`.`income` <= '30000' AND `csi`.`orientation` = 1 AND `csi`.`dot` IN ('1', '2', '3') GROUP BY `csi`.`LCDScreen_id`;

   [add index] 
   	   field name 
		LCDScreenPricesAssigned.LCDScreen_id[index](necessary)

9、[parse sql]

   SELECT `LCDScreenTimeRemaining`.`id` AS `id`, `LCDScreenTimeRemaining`.`date` AS `date`, `LCDScreenTimeRemaining`.`timeRemainingSec_peak` AS `timeRemainingSec_peak`, `LCDScreenTimeRemaining`.`timeRemainingSec_offpeak` AS `timeRemainingSec_offpeak` FROM `LCDScreenTimeRemaining` WHERE `LCDScreen_id` = 277;

   [add index] 
   	   field name 
		LCDScreenTimeRemaining.LCDScreen_id[index](necessary)

10、[parse sql]

   SELECT `CRM_userTable`.*, (CASE WHEN `password` = md5('passwordJohnny') THEN 1 ELSE 0 END) AS `zend_auth_credential_match` FROM `CRM_userTable` WHERE `userName` = 'tedcoco';		

   [add UNIQUE] 
   	   field name 
		CRM_userTable.userName[UNIQUE](necessary)
11、[parse sql]

   SELECT `main`.*, `curp`.`CRM_userPermissions_id` AS `curp_CRM_userPermissions_id`, `cucd`.`companyName` AS `cucd_companyName` FROM `CRM_userTable` AS `main` LEFT JOIN `CRM_userRolePermissions` AS `curp` ON `curp`.`CRM_userRoles_id`=`main`.`CRM_userRoles_id` LEFT JOIN `CRM_userCompanyDetails` AS `cucd` ON `cucd`.`id`=`main`.`CRM_userCompanyDetails_id` WHERE `main`.`CRM_userCompanyDetails_id` = '127';

   [add INDEX] 
   	   field name 
		CRM_userRolePermissions.CRM_userRoles_id[INDEX](necessary)



14、[parse sql]

   explain SELECT `locationContact`.* FROM `locationContact` WHERE `locations_id` = '312';
   SELECT `locationContact`.* FROM `locationContact` WHERE `locationBodyCorporate_id` = '303';
   [add INDEX] 
         field name 
      locationContact.locations_id[INDEX]
      locationContact.locationBodyCorporate_id[INDEX]
15、[parse sql]

   SELECT `cppls`.*, `slsc`.`description` AS `subDes`, `slc`.`description` AS `pDes` FROM `campaignProPosedLocationSubCategory` AS `cppls` LEFT JOIN `locationSubCategories` AS `slsc` ON `slsc`.`id` = `cppls`.`locationSubCategories_id` LEFT JOIN `locationCategories` AS `slc` ON `slc`.`id` = `slsc`.`locationCategories_id` WHERE `cppls`.`CRM_campaignProposed_id` = '94';

   [add INDEX] 
         field name 
      campaignProPosedLocationSubCategory.CRM_campaignProposed_id[INDEX]
16、[parse sql]

   SELECT `m`.`description` AS `description`, `m`.`title` AS `title`, concat(resolutionWidth ,"x",resolutionHeight) AS `resolution`, `m`.`filePath` AS `filePath`, `m`.`metatag` AS `metatag`, `m`.`approved` AS `approved`, `m`.`length` AS `length`, `m`.`id` AS `mid`, `m`.`used` AS `used`, `m`.`thumbPath` AS `thumbNail`, `m`.`originalThumbPath` AS `originalThumbPath`, `m`.`uploadDateTime` AS `upload_date`, `m`.`mediaClass` AS `mediaClass`, `m`.`type` AS `type`, `m`.`transcodedMP4FilePath` AS `transcodedMP4FilePath`, `m`.`transcodedWEBMFilePath` AS `transcodedWEBMFilePath`, `m`.`CRM_userTable_id` AS `CRM_userTable_id`, `a`.`albumTitle` AS `cate`, `a`.`id` AS `cid`, `sd`.`companyName` AS `scom` FROM `sponsorMedia` AS `m` LEFT JOIN `sponsorMediaAlbumContent` AS `c` ON `c`.`sponsorMedia_id` = `m`.`id` LEFT JOIN `sponsorMediaAlbums` AS `a` ON `a`.`id`=`c`.`sponsorMediaAlbums_id` LEFT JOIN `CRM_userCompanyDetails` AS `sd` ON `sd`.`id`=`m`.`CRM_userCompanyDetails_id` WHERE `m`.`id` = '499';
   [add INDEX] 
         field name 
      sponsorMediaAlbumContent.sponsorMedia_id[INDEX]
17、[parse sql]

   SELECT `rc`.`cityTable_id` AS `cityTable_id`, `ct`.`id` AS `id`, `ct`.`city` AS `city`, `ct`.`provinceTable_id` AS `province_id` FROM `CRM_iMONRecommendedBasePrice` AS `rc` LEFT JOIN `cityTable` AS `ct` ON `rc`.`cityTable_id`=`ct`.`id` WHERE `rc`.`expiryDate` = '0000-00-00 00:00:00' ORDER BY `province_id` ASC;
   [add INDEX] 
         field name 
      CRM_iMONRecommendedBasePrice.expiryDate[INDEX]
18、[parse sql]
   SELECT `d`.*, `b`.`companyName` AS `companyName`, `f`.`description` AS `category` FROM `sponsorMedia` AS `d` LEFT JOIN `CRM_userCompanyDetails` AS `b` ON `d`.`CRM_userCompanyDetails_id` = `b`.`id` LEFT JOIN `sponsorMediaCategories` AS `f` ON `f`.`id`=`d`.`sponsorMediaCategories_id` WHERE `d`.`type` = 11 AND `d`.`approved` = 0 AND `d`.`deleted` = 0 AND `b`.`id` = '128';
   [add INDEX] 
         field name 
      sponsorMedia.type and sponsorMedia.approved[GROUP INDEX]
19、[parse sql]
   SELECT `main`.`id` AS `pid`, `main`.`province` AS `province`, `ct`.`id` AS `cid`, `ct`.`city` AS `city` FROM `provinceTable` AS `main` LEFT JOIN `cityTable` AS `ct` ON `ct`.`provinceTable_id` = `main`.`id`;
   [add INDEX] 
         field name 
      cityTable.provinceTable_id [INDEX]
20、[parse sql]
   SELECT `main`.*, `s`.`finalCost` AS `cost`, `s`.`title` AS `title`, `s`.`description` AS `description`, `s`.`startDate` AS `startDate`, `s`.`endDate` AS `endDate`, `s`.`playlistDuration` AS `playlistDuration`, `s`.`offPeakPPD` AS `offPeakPPD`, `s`.`peakPPD` AS `peakPPD`, `l`.`cityTable_id` AS `cid`, `ucom`.`companyName` AS `companyName` FROM `CRM_shoppingCart` AS `main` LEFT JOIN `CRM_campaignProposed` AS `s` ON `main`.`CRM_campaignProposed_id` = `s`.`id` LEFT JOIN `CRM_campaignCityData` AS `l` ON `l`.`CRM_campaignProposed_id`=`s`.`id` LEFT JOIN `CRM_userCompanyDetails` AS `ucom` ON `ucom`.`id`=`s`.`CRM_userCompanyDetails_id` WHERE `main`.`id` = '1' OR `main`.`id` = '2' OR `main`.`id` = '4';
   [add INDEX] 
         field name 
      CRM_campaignCityData.CRM_campaignProposed_id [INDEX]
==================================================================================创建相应的索引====================================================================
#创建相应的索引
CREATE INDEX index_CRM_userPermissions_id ON CRM_userRolePermissions (CRM_userPermissions_id);
CREATE INDEX index_CRM_userRoles_id ON CRM_userRolePermissions (CRM_userRoles_id);
CREATE INDEX index_location_id ON LCDScreen (location_id);
CREATE INDEX index_CRM_userCompanyDetails_id ON LCDScreen (CRM_userCompanyDetails_id);
CREATE INDEX index_LCDTimes_id ON LCDTimesSaved (LCDTimes_id);
CREATE INDEX index_LCDScreen_id ON LCDScreenTimes (LCDScreen_id);
CREATE INDEX index_LCDScreen_id ON report_campaignHistory (LCDScreen_id);
CREATE INDEX index_CRM_campaignProposed_id ON CRM_campaignSelectedMedia (CRM_campaignProposed_id);
CREATE INDEX index_cityTable_id ON CRM_CFDValues (cityTable_id);
CREATE INDEX index_cityTable_id ON CRM_MCbasePrice (cityTable_id);
CREATE INDEX index_LCDScreen_id ON CRM_campaignSelectedScreens (LCDScreen_id);
CREATE INDEX index_CRM_campaignProposed_id ON CRM_campaignSelectedScreens (CRM_campaignProposed_id);
CREATE INDEX index_LCDScreen_id ON LCDScreenPricesAssigned (LCDScreen_id);
CREATE INDEX index_LCDScreen_id ON LCDScreenTimeRemaining (LCDScreen_id);
CREATE INDEX index_userName ON CRM_userTable (userName);
CREATE INDEX index_locations_id ON locationContact (locations_id);
CREATE INDEX index_locationBodyCorporate_id ON locationContact (locationBodyCorporate_id);
CREATE INDEX index_CRM_campaignProposed_id ON campaignProPosedLocationSubCategory (CRM_campaignProposed_id);
CREATE INDEX index_sponsorMedia_id ON sponsorMediaAlbumContent (sponsorMedia_id);
CREATE INDEX index_expiryDate ON CRM_iMONRecommendedBasePrice (expiryDate);
ALTER TABLE sponsorMedia ADD INDEX Group_index_TypeAndApproved (approved,type); 
CREATE INDEX index_provinceTable_id ON cityTable (provinceTable_id);
CREATE INDEX index_CRM_campaignProposed_id ON CRM_campaignCityData (CRM_campaignProposed_id);

=================================================================================删除sql 索引==========================================================
#删除sql 索引
DROP INDEX GROUP_INDEX_TypeAndApproved ON sponsorMedia;
DROP INDEX index_CRM_userPermissions_id ON CRM_userRolePermissions;
DROP INDEX index_CRM_userRoles_id ON CRM_userRolePermissions;
DROP INDEX index_location_id ON LCDScreen;
DROP INDEX index_CRM_userCompanyDetails_id ON LCDScreen;
DROP INDEX index_LCDTimes_id ON LCDTimesSaved;
DROP INDEX index_LCDScreen_id ON LCDScreenTimes;
DROP INDEX index_LCDScreen_id ON report_campaignHistory;
DROP INDEX index_CRM_campaignProposed_id ON CRM_campaignSelectedMedia;
DROP INDEX index_cityTable_id ON CRM_CFDValues;
DROP INDEX index_cityTable_id ON CRM_MCbasePrice;
DROP INDEX index_LCDScreen_id ON CRM_campaignSelectedScreens;
DROP INDEX index_CRM_campaignProposed_id ON CRM_campaignSelectedScreens;
DROP INDEX index_LCDScreen_id ON LCDScreenPricesAssigned;
DROP INDEX index_LCDScreen_id ON LCDScreenTimeRemaining;
DROP INDEX index_userName ON CRM_userTable;
DROP INDEX index_locations_id ON locationContact;
DROP INDEX index_locationBodyCorporate_id ON locationContact;
DROP INDEX index_CRM_campaignProposed_id ON campaignProPosedLocationSubCategory;
DROP INDEX index_sponsorMedia_id ON sponsorMediaAlbumContent;
DROP INDEX index_expiryDate ON CRM_iMONRecommendedBasePrice;
DROP INDEX index_provinceTable_id ON cityTable;
DROP INDEX index_CRM_campaignProposed_id ON CRM_campaignCityData;
