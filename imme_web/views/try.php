		<div class="section-1">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6">
            <ul class="list-inline try-menu">
              <li>
                <a href="http://imme.asia"><img src="<?php echo base_url("assets/images/logo-white.png"); ?>" class="header-logo"></a>
              </li>
              <li class="hidden-sm hidden-xs"><a href="#VoucherDeposit">Voucher Deposit</a></li>
              <li class="hidden-sm hidden-xs"><a href="#PayMerchant">Pay Merchant</a></li>
              <li class="hidden-sm hidden-xs"><a href="#CollectCoin">Collect Coin</a></li>
              <li class="hidden-sm hidden-xs"><a href="#SendMoney">Send Money</a></li>
            </ul>
          </div>
          <div class="col-md-6 hidden-xs">
            <ul class="list-inline list-unstyled pull-right try-menu-user">
              <li><a href="#PersonalBank" data-toggle="modal" data-target="#notReadyModal">Personal</a></li>
              <li><a href="#MerchantDashboard" data-toggle="modal" data-target="#notReadyModal">Merchant</a></li>
              <li><a href="#OpenAPI" data-toggle="modal" data-target="#notReadyModal">Developer</a></li>
              <li><a href="#Login" data-toggle="modal" data-target="#notReadyModal">Login</a></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="container section1-container">
        <div class="col-md-offset-1 col-md-6">
          <h1 class="try-title">
            Meet your exclusive partner for every daily needs.
          </h1>
          <p class="try-desc">
            IMME is your personal wallet who allow you to do transaction with our merchant or with other
            IMME users. You can keep your change from our vendors in your IMME wallet, transfer your 
            money to your bank account or send some of your money to your friends with QR code.
          </p>
          <button class="btn btn-success btn-get-beta-app">Get Beta App</button>
        </div>

        <div class="col-md-4 text-center">
          <h3 class="scan-title">Scan this voucher code by tap deposit inside imme app</h3>
          <img src="<?php echo $voucher; ?>" class="voucher-image" width="100%">
        </div>
      </div>
		</div>

    <a name="ForPayment" id="ForPayment"></a>
    <div class="section-2">
      <div class="container">
        <div class="row">
          
          <div class="col-md-5 white">
            <div class="section-title">IMME for Payment</div>
            <p class="section-description">
              IMME is very simple apps, make your payment very easy.
            </p>
            <ol class="section-description">
              <li>Tap the transfer icon</li>
              <li>Scan the barcode</li>
              <li>Input your pin 1</li>
            </ol>
            <p class="section-note">
              Try scan the barcode, this payment will not reduce your balance.
            </p>
          </div>
          <div class="col-md-7">
            <img src="<?php echo $payment; ?>" width="100%">
          </div>
        </div>
      </div>
    </div>
    
    <a name="CollectMoney" id="CollectMoney"></a>
    <div class="section-3">
      <div class="container">
        <div class="row">
          <div class="col-md-offset-1 col-md-3">
            <img src="<?php echo $collect; ?>">
          </div>
          
          <div class="col-md-offset-1 col-md-6">
            <div class="section-title-3">Collect money from cash payment</div>
            <p class="section-description-3">
              When you had paid from merchant and get coins, you can made voucher code.
            </p>
            <ol class="section-description-3">
              <li>Tap the deposit icon</li>
              <li>Scan the barcode</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    
    <div class="content2">
      <div class="container">
        <div class="row content">
          <div class="col-md-offset-1 col-md-6">
            <div class="section-title-3">Let's Try Send Money</div>
            <p class="section-description-3">
              Send money is realy easy in 3 step :
            </p>
            <ol class="section-description-3">
              <li>Tap the transfer icon</li>
              <li>Scan your friend barcode</li>
              <li>Input your PIN 1</li>
            </ol>
            <p class="section-note-3">
              Try scan the barcode, this payment will not reduce your balance.
            </p>
          </div>
          
          <div class="col-md-3">
            <img src="<?php echo base_url("assets/images/imme-receive-template.png"); ?>">
          </div>
        </div>
      </div>
    </div>
    
    <a name="VoteYourCity" id="VoteYourCity"></a>
    <div class="container">
      <div class="row content">
        <div class="col-lg-offset-2 col-lg-5 col-md-5 col-sm-5 col-xs-12">
          <h2>Be an immenia!</h2>
          <p><b>Join the experiment and get merchandise!</b></p>
          <p>
            When we will launch new feature, be the first who know and get the merchendise!
            You will get Hat, T-shirt, Stickers and many more!
          </p>
        </div>
        
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 vote-location text-center">
          <h4>Spread this good idea</h4>
          <h5>From indonesia to The World</h5>
          <div class='shareaholic-canvas' data-app='share_buttons' data-app-id='23006544'></div>
        </div>
      </div>
    </div>
    
    <div class="section4">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center contact-us">
            <h3 class="contact-us-title">Contact Us</h3>
            <p>Don't be shy, if you have an idea, support, and ask, send it now!<br>
              feel free to fill this form</p>
          </div>
          
          <form>
            <div class="col-md-offset-4 col-md-4 text-right">
              <div class="form-group">
                <textarea class="form-control" rows="3" placeholder="Fill here..."></textarea>
              </div>
                <button type="submit" class="btn btn-default">Send Message</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="notReadyModal" tabindex="-1" role="dialog" aria-labelledby="notReadyModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="notReadyModalLabel">Ouch.. seems we're not ready..</h4>
          </div>
          <div class="modal-body text-center">
            <h4>We are cooking the code, please wait ...</h4>
            If you have any question, please let us to know ...

            <div id="notReadyLoading" class="loading text-center" style="display:none;"><img src="<?php echo base_url("assets/images/loading-1x.gif"); ?>"></div>
            <div id="notReadyMessage" class="text-center" style="display:none;padding:1em;"></div>

            <form class="download" id="notReadyForm">
              <div class="form-group">
                <textarea id="notReady-question" class="form-control" placeholder="Type your question here ..."></textarea>
              </div>
              <button type="submit" class="btn btn-default">Ask to IMME</button>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default white-button" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="emailModalLabel">Vote my city!</h4>
          </div>
          <div class="modal-body">
            Please insert your email

            <div id="emailLoading" class="loading text-center" style="display:none;"><img src="<?php echo base_url("assets/images/loading-1x.gif"); ?>"></div>

            <form id="emailForm" class="form-inline download text-center">
              <div class="form-group">
                <input type="email" class="form-control" id="email-email" placeholder="your@email.com">
              </div>
              <button type="submit" class="btn btn-default">OK</button>
            </form>

            <div id="emailMessage" class="text-center" style="display:none;padding:1em;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default white-button" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>