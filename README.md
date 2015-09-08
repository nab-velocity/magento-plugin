Velocity Magento Module Installation Documentation 

1.	Download velocity Magento Module by clicking on Download zip button on the right bottom of this page.

2.	Configuration Requirement: Magento site Version 1.9 or above version must be required for our velocity payment module installation.


3.	Installation & Configuration of Module from Admin Panel:
	  Login Magento admin panel and goto System Menu option then Magento connect option then Magento Connect Manager select display a panel for login page then login with same admin credential of magento admin panel.

	  Show two option for add the module one for online by url and other one is upload maudule package .tgz file only and all installed module listed bellow.

	  Click on “Browse” option and select .tgz module file from system then Click on “Upload” button for upload the module in Magento module section and listed in “MODULES LIST” below.

	  After Successful installation, click on top right corner 'Return to Admin' goto System -> Configuration then left side listed options select 'Payment Methods' and configure the velocity credentials.

VELOCITY CREDENTIAL DETAILS
1.	IdentityToken: - This is security token provided by velocity to merchant.
2.	WorkFlowId/ServiceId: - This is service id provided by velocity to merchant.
3.	ApplicationProfileId: - This is application id provided by velocity to merchant.
4.	MerchantProfileId: - This is merchant id provided by velocity to merchant.
5.	Test Mode :- This is for test the module, if select dropdwon 'yes' then test mode enable and no need to save “WorkFlowId/ServiceId & MerchantProfileId” otherwise select 'NO' from dropdwon and save  “WorkFlowId/ServiceId & MerchantProfileId” for live payment.

For Refund option at admin side from sales menu click on order option and selct invoice and option then right top cleck on 'Create Memo' and right bottom shows two type of refund one for offline and other of online then put refund ammount and process for refund.

For uninstall the velocity module of magento goto System Menu option then Magento connect option then Magento Connect Manager select display a panel for login page then login with same admin credential of magento admin panel.

Show all instaled module listed bellow select unistall from drupdown and click on 'Commit Changes' button.

4.  We have saved the raw request and response objects in &lt;prefix&gt;_velocity_transactions table.