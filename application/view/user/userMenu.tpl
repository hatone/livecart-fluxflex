<div id="userMenuContainer">
	<ul id="userMenu">
	   <li id="homeMenu" class="{if "homeMenu" == $current}selected{/if}"><a href="{link controller=user}">{t _acc_home}</a></li>

	   <li id="orderMenu" class="{if "orderMenu" == $current}selected{/if}"><a href="{link controller=user action=orders}">{t _orders}</a></li>
	   <li id="fileMenu" class="{if "fileMenu" == $current}selected{/if}"><a href="{link controller=user action=files}">{t _downloads}</a></li>

	   <li id="personalMenu" class="{if "personalMenu" == $current}selected{/if}"><a href="{link controller=user action=personal}">{t _personal_info}</a></li>
	   <li id="addressMenu" class="{if "addressMenu" == $current}selected{/if}"><a href="{link controller=user action=addresses}">{t _addresses}</a></li>
	   <li id="emailMenu" class="{if "emailMenu" == $current}selected{/if}"><a href="{link controller=user action=changeEmail}">{t _change_email_address}</a></li>
	   <li id="passwordMenu" class="{if "passwordMenu" == $current}selected{/if}"><a href="{link controller=user action=changePassword}">{t _change_pass}</a></li>

	   {block USER_MENU_ITEMS}

	   <li id="signOutMenu" class="{if "signoutMenu" == $current}selected{/if}"><a href="{link controller=user action=logout}">{t _sign_out}</a></li>
	</ul>
</div>