<?php
session_start();
include("config.php");
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \mPDF();

ob_start();
$back = $bdd->query("SELECT * FROM documents WHERE id='".$_GET['id']."'");
$facture = $back->fetch();
$back = $bdd->query("SELECT * FROM companies WHERE id='".$facture['company']."'");
$company = $back->fetch();
?>
<!DOCTYPE html>
<html lang="zxx">
	<head>
		<meta charset="utf-8">
		<style>
			@page {
				<?php
				if(isset($_GET['header'])){
					if($_GET['header'] == "1"){
						?>
				margin:25px 25px 280px 25px;
						<?php
					}
					elseif($_GET['header'] == "0"){
						?>
				margin:180px 25px 280px 25px;
						<?php						
					}
				}
				else{
					?>
				margin:25px 25px 280px 25px;
					<?php					
				}
				?>
				header: html_MyCustomHeader;
				footer: html_MyCustomFooter;
			}
		</style>
	</head>
	<body>
		<div style="padding:20px;font-family:'Arial';">
			<table style="width:100%;font-family:'Arial';">
				<tr>
					<td style="width:50%;">
						<?php
						if(isset($_GET['header'])){
							if($_GET['header'] == "1"){
								?>
						<img src="uploads/<?php echo $company['logo1'];?>" style="max-height:90px;max-width:160px;" />
								<?php
							}
						}
						else{
							?>
						<img src="uploads/<?php echo $company['logo1'];?>" style="max-height:90px;max-width:160px;" />
							<?php					
						}
						?>
					</td>
					<?php
					$back = $bdd->query("SELECT * FROM suppliers WHERE id='".$facture['supplier']."'");
					$supplier = $back->fetch();
					?>
					<td style="width:50%;text-align:right;">
						<p style="font-weight:bold;">Bon de réception: <?php echo $facture['code'];?></p>
						<p style="font-size:12px;">Date création: <?php echo date("d/m/Y",$facture['dateadd']);?></p>
						<p style="font-size:12px;"><?php echo ($supplier['codefo']!=""?"Code Fournisseur: ".$supplier['codefo']:"Réf Fournisseur: ".$supplier['code']);?></p>
					</td>
				</tr>
			</table>
			<table style="width:100%;margin-top:30px;font-family:'Arial';">
				<tr>
					<td><p style="font-size:12px;">Émetteur</p></td>
					<td></td>
					<td><p style="font-size:12px;">Adressé à</p></td>
				</tr>
				<tr>
					<td style="width:50%;padding:10px;background:#EEEEEE;">
						<p style="font-weight:bold;"><?php echo $company['rs'];?></p>
						<p style="font-size:12px;"><?php echo $company['address'];?></p>
						<p style="font-size:12px;"><b>Tél.: </b><?php echo $company['phone'];?></p>
						<?php
						if($company['email'] != ""){
						?>
						<p style="font-size:12px;"><b>Email: </b><?php echo $company['email'];?></p>
						<?php
						}
						if($company['website'] != ""){
						?>
						<p style="font-size:12px;"><b>Siteweb: </b>: <?php echo $company['website'];?></p>
						<?php
						}
						?>
					</td>
					<td style="width:1%;"></td>
					<td style="width:49%;padding:10px;border:1px solid #000000;">
						<p style="font-weight:bold;"><?php echo $supplier['title']!=""?$supplier['title']:"Fournisseur anonyme";?></p>
						<?php
						if($supplier['address'] != ""){
						?>
						<p style="font-size:12px;"><b>Adresse: </b><?php echo $supplier['address']?></p>
						<?php
						}
						if($supplier['respphone'] != ""){
						?>
						<p style="font-size:12px;"><b>Tél: </b><?php echo $supplier['respphone']?></p>
						<?php
						}
						if($supplier['respemail'] != ""){
						?>
						<p style="font-size:12px;"><b>Email: </b><?php echo $supplier['respemail']?></p>
						<?php
						}
						if($supplier['respfax'] != ""){
						?>
						<p style="font-size:12px;"><b>Fax: </b><?php echo $supplier['respfax']?></p>
						<?php
						}
						?>
					</td>
				</tr>
			</table>
			<?php
			if($facture['abovetable'] != ""){
			?>
			<table style="margin:20px 0px 10px;width:100%;border-collapse:collapse;font-family:'Arial';font-size:12px;font-weight:bold;">
				<tr>
					<td valign="top" align="left" style="padding:4px;width:65%;font-weight:normal;">
						<?php
						echo "<b>Note: </b>".$facture['abovetable'];
						?>
					</td>
				</tr>
			</table>
			<?php
			}
			?>
			<table style="margin-top:10px;border-collapse:collapse;width:100%;font-size:12px;font-family:'Arial';text-align:center;">
				<?php
				$discounttype = "";
				$back = $bdd->query("SELECT * FROM detailsdocuments WHERE doc IN(".$facture['id'].")");
				while($command = $back->fetch()){
					if($command['discount'] > 0){
						$existdiscount = 1;
						$discounttype = $command['discounttype'];
					}
				}					
				?>
				<thead>
					<tr>
						<th style="width:65%;padding:4px;border:1px solid #000000;">Désignation</th>
						<th style="padding:4px;border:1px solid #000000;">Qté</th>
					</tr>
				</thead>
				<?php
				$j = 1;
				$back = $bdd->query("SELECT * FROM detailsdocuments WHERE doc IN(".$facture['id'].") ");
				while($command = $back->fetch()){
					?>
				<tr>
					<td style="height:20px;padding:4px;border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000;text-align:left;">
<?php echo $command['title']; ?></td>
<td style="padding:4px;border-top:1px solid #000000;border-right:1px solid #000000;text-align:right;white-space:nowrap;">
<?php echo $command['qty']." ".$command['unit']; ?></td>
</tr>
<?php
$j++;
}
?>
				<tr>
					<td style="height:20px;border-right:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;"></td>
					<td style="border-right:1px solid #000000;border-bottom:1px solid #000000;"></td>
				</tr>
			</table>
			<!-- Removed summary table and all price/total related code below -->
		</div>
		<htmlpagefooter name="MyCustomFooter">
			<div class="footer">
				<p style="margin:10px;text-align:center;font-family:arial;font-size:12px;text-decoration:underline;">Cachet et signature:</p>
				<?php
				if(isset($_GET['signature'])){
					if($_GET['signature'] == "1" AND $company['signature'] !== ""){
					?>
				<p style="padding:0px 50px 30px 0px;text-align:right;"><img src="uploads/<?php echo $company['signature'];?>" style="display:block;height:100px;margin:10px auto;" /></p>
					<?php
					}
					else{
					?>
				<p style="padding:0px 50px 30px 0px;text-align:right;"><img src="images/fakesignature.png" style="display:block;height:100px;margin:10px auto;" /></p>
					<?php					
					}
				}
				if(isset($_GET['header'])){
					if($_GET['header'] == "1"){
						?>
						<p style='padding:5px 0px 0px 0px;font-family:arial;font-size:9px;text-align:center;border-top:1px solid #000000;'><b>Siège social: </b><?php echo $company['rs'];?> - <?php echo $company['address'];?><br />
						<?php
						if($company['phone'] != ""){
							?>
						<b>Téléphone: </b><?php echo $company['phone'];?> 
							<?php
						}
						if($company['email'] != ""){
							?>
						- <b>E-mail: </b><?php echo $company['email'];?>
							<?php
						}
						if($company['website'] != ""){
							?>
						- <b>Siteweb: </b><?php echo $company['website'];?>
							<?php
						}
						?>
						<br />
						<?php
						if($company['capital'] != ""){
							?>
						<b>Capital de </b><?php echo $company['capital'];?> 
							<?php
						}
						if($company['rc'] != ""){
							?>
						- <b>R.C.: </b><?php echo $company['rc'];?> 
							<?php
						}
						if($company['patente'] != ""){
							?>
						- <b>Patente: </b><?php echo $company['patente'];?>
							<?php
						}
						?>
						<br />
						<?php
						if($company['iff'] != ""){
							?>
						<b>I.F.: </b><?php echo $company['iff'];?> 
							<?php
						}
						if($company['cnss'] != ""){
							?>
						- <b>C.N.S.S.: </b><?php echo $company['cnss'];?> 
							<?php
						}
						if($company['ice'] != ""){
							?>
						- <b>ICE: </b><?php echo $company['ice'];?>
							<?php
						}
						?>
						<br /><b>Page: {PAGENO}/{nbpg}</b></p>
						<?php
					}
				}
				else{
					?>
					<p style='padding:5px 0px 0px 0px;font-family:arial;font-size:9px;text-align:center;border-top:1px solid #000000;'><b>Siège social: </b><?php echo $company['rs'];?> - <?php echo $company['address'];?><br />
					<?php
					if($company['phone'] != ""){
						?>
					<b>Téléphone: </b><?php echo $company['phone'];?> 
						<?php
					}
					if($company['email'] != ""){
						?>
					- <b>E-mail: </b><?php echo $company['email'];?>
						<?php
					}
					if($company['website'] != ""){
						?>
					- <b>Siteweb: </b><?php echo $company['website'];?>
						<?php
					}
					?>
					<br />
					<?php
					if($company['capital'] != ""){
						?>
					<b>Capital de </b><?php echo $company['capital'];?> 
						<?php
					}
					if($company['rc'] != ""){
						?>
					- <b>R.C.: </b><?php echo $company['rc'];?> 
						<?php
					}
					if($company['patente'] != ""){
						?>
					- <b>Patente: </b><?php echo $company['patente'];?>
						<?php
					}
					?>
					<br />
					<?php
					if($company['iff'] != ""){
						?>
					<b>I.F.: </b><?php echo $company['iff'];?> 
						<?php
					}
					if($company['cnss'] != ""){
						?>
					- <b>C.N.S.S.: </b><?php echo $company['cnss'];?> 
						<?php
					}
					if($company['ice'] != ""){
						?>
					- <b>ICE: </b><?php echo $company['ice'];?>
						<?php
					}
					?>
					<br /><b>Page: {PAGENO}/{nbpg}</b></p>
					<?php
				}
				?>
			</div>
		</htmlpagefooter>
	</body>
</html>
<?php
$content = ob_get_clean();
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$mpdf->WriteHTML($content);
$mpdf->Output($facture['code'].'.pdf','D');
?>