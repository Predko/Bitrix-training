
<?php
use Bitrix\Main\Page\Asset;
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"fishfarm-bottom-menu", 
	array(
		"ROOT_MENU_TYPE" => "bottom_menu",
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "bottom_menu",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"COMPONENT_TEMPLATE" => "fishfarm-bottom-menu",
		"MENU_THEME" => "yellow"
	),
	false
);?>

<!-- Contact Section -->
<section class="page-section" id="contact">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <h2 class="mt-0">Связаться с нами</h2>
    <hr class="divider my-4">
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4 ml-auto text-center mb-5 mb-lg-0">
        <i class="fas fa-phone fa-3x mb-3 text-muted"></i>
        <div>+1 (202) 555-0149</div>
      </div>
      <div class="col-lg-4 mr-auto text-center">
        <i class="fas fa-envelope fa-3x mb-3 text-muted"></i>
        <!-- Make sure to change the email address in anchor text AND the link below! -->
        <a class="d-block" href="mailto:contact@yourwebsite.com">contact@yourwebsite.com</a>
      </div>
    </div>
  </div>
</section>

  <!-- Footer -->
  <footer class="bg-light py-5">
    <div class="container">
      <div class="small text-center text-muted">Copyright &copy; 2019 - Start Bootstrap<br/>Copyright © Blackrock Digital LLC. Code released under the MIT license.</div>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <!-- <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>-->
  <?Asset::getInstance()->AddJs(SITE_TEMPLATE_PATH."/vendor/jquery/jquery.min.js");?>
  <?Asset::getInstance()->AddJs(SITE_TEMPLATE_PATH."/vendor/bootstrap/js/bootstrap.bundle.min.js");?>

  <!-- Plugin JavaScript -->
  <!-- <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/magnific-popup/jquery.magnific-popup.min.js"></script>  -->
  <?Asset::getInstance()->AddJs(SITE_TEMPLATE_PATH."/vendor/jquery-easing/jquery.easing.min.js");?>
  <?Asset::getInstance()->AddJs(SITE_TEMPLATE_PATH."/vendor/magnific-popup/jquery.magnific-popup.min.js");?>

  <!-- Custom scripts for this template -->
  <!-- <script src="js/creative.min.js"></script> -->
  <?Asset::getInstance()->AddJs(SITE_TEMPLATE_PATH."/js/creative.min.js");?>

</body>

</html>
