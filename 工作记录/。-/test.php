<?php
<div class='selectedScreen clearBoth'>
	<table class="table table-striped">
		<tr>		        
			<th><?php echo $this->translate('Screen Serial Number')?></th>
			<th><?php echo $this->translate('Media Company')?></th>
			<th><?php echo $this->translate('Country')?>
				<select id='countrySelect'>
	    	 		<option value='0'><?php echo $this->translate('ALL') ?></option>
		     	 	<?php if(!empty($this->conutry)):foreach ($this->conutry as $key=>$country): ?>
		     	 		<option <?php echo ($this->selectCountry == $key)? "selected='selected'":'';?> value='<?php echo $key; ?>'><?php echo $country; ?></option>
		     	 	<?php endforeach;endif; ?>
	    		</select>
			</th>
			<th>
				<?php echo $this->translate('Province ')?>
				<select id='provinceSelect'>
	    	 		<option value='0'><?php echo $this->translate('ALL') ?></option>
		     	 	<?php if(!empty($this->province)): foreach ($this->province as $key=>$province): ?>
		     	 		<option <?php echo ($this->selectProvince == $key)? "selected='selected'":'';?> value='<?php echo $key; ?>'><?php echo $province; ?></option>
		     	 	<?php endforeach; endif;?>
	    		</select>
			</th>
			<th>
				<?php echo $this->translate('City')?>
				<select id='citySelect'>
	    	 		<option value='0'><?php echo $this->translate('ALL') ?></option>
		     	 	<?php if(!empty($city)): foreach ($this->city as $key=>$city): ?>
		     	 		<option <?php echo ($this->selectCity == $key)? "selected='selected'":'';?> value='<?php echo $key; ?>'><?php echo $city; ?></option>
		     	 	<?php endforeach;endif; ?>
	    		</select>
			</th>
			<th><?php echo $this->translate('Location Details')?>
				<input type='text' id='searchDetails' value='<?php echo $this->searchStr ?>'  />
			</th>
			<th><?php echo $this->translate('Screen Sponsor')?></th>
			<th><?php echo $this->translate('Sponsor Name')?></th>
		</tr>
		<?php foreach ($screenList as $list):?>
		<?php if(!empty($list)):?>
			<tr>
				<td><a href="<?php echo $this->url('campaign',array('action'=>'', 'id' => $list->serialNumber));?>"><?=$list->serialNumber?></a></td>
				<td><?=$list->companyName?></td>
				<td><?=$list->conutry?></td>
				<td><?=$list->province?></td>
				<td><?=$list->city?></td>
				<td><?=$list->streetName.$list->buildingName.$list->buildingNumber.$list->locationDescription?></td>
				<td><?php if ($list->spCompanyName != null){ echo 'Y';}else{echo 'N';} ?></td>
				<td><?=$list->spCompanyName?></td>
			</tr>
			<?php endif;?>
		<?php endforeach;?>
	</table>
</div>

 SELECT `cc`.`id` AS `id`, null AS `runningDays`, null AS `title`, `cc`.`description` AS `description`, null AS `purchasedDateTime`, null AS `purchased`, 13 AS `totalScreenNum`,
  "cartoonCampaign" AS `campaignType`, null AS `companyName`, `cc`.`startDate` AS `startDateTime`, `cc`.`endDate` AS `endDateTime`, `cc`.`endDate` AS `mendDateTime`, null AS Expression1 
  FROM `cartoonCampaigns` AS `cc` LEFT JOIN `CartoonSponsors` AS `cs` ON `cc`.`cartoonSponsors_id` = `cs`.`id` LEFT JOIN `sponsorDetails` AS `sd` ON `cs`.`sponsorDetails_id` = `sd`.`id` 
  LEFT JOIN `CRM_userCompanyDetails` AS `uc` ON `sd`.`CRM_userCompanyDetails_id` = `uc`.`id` WHERE `uc`.`id` = '1' AND `cc`.`disabled` = '0' AND unix_timestamp(cc.endDate) < '1392912000'
  refs #58 #59 #60 #61 
you can test: 
1.launch Campaign step four save data to DB
2. cartoon launch campaign
3. cartoon list
Change-Id: I0000000000000000000000000000000000000000

http://gerrit.imon.local:8080/195

}else{
							foreach ($time as $skey1=>$sval1){
// 								if(!empty($sval1) && in_array($sval1,$val)){
// 									unset($time[$skey1]);
// 								}
								$screenSernum = $this->getScreenScreenSerialNumber($skey1);
								if(!empty($sval) && $key==$screenSernum){
									foreach ($sval1 as $skey2=>$sval2){
										if(!empty($sval2) && in_array($sval2,$val)){
											unset($time[$skey1][$skey2]);
										}

									}
								}

							}
						}
?>
      