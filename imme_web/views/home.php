		<div class="container-fluid header">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6">
            <ul class="list-inline header-menu-first">
              <li>
                <a href="http://imme.asia"><img src="<?php echo base_url("assets/images/logo.png"); ?>" class="header-logo"></a>
              </li>
              <li class="hidden-sm hidden-xs"><a href="#Calculate">Save Your Coins</a></li>
              <li class="hidden-sm hidden-xs"><a href="#TryIMME">Try Wallet</a></li>
              <li class="hidden-sm hidden-xs"><a href="#VoteYourCity">Vote your city</a></li>
            </ul>
          </div>
          <div class="col-md-6 hidden-xs">
            <ul class="list-inline list-unstyled pull-right header-menu-secondary">
              <li><a href="#PersonalBank" data-toggle="modal" data-target="#notReadyModal">Personal</a></li>
              <li><a href="#MerchantDashboard" data-toggle="modal" data-target="#notReadyModal">Merchant</a></li>
              <li><a href="#OpenAPI" data-toggle="modal" data-target="#notReadyModal">Developer</a></li>
              <li><a href="#Login" data-toggle="modal" data-target="#notReadyModal">Login</a></li>
            </ul>
          </div>
        </div>
  		</div>
    </div>

		<div class="container-fluid slider">
      <div class="container">
        <div class="row">
          <div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 text-center slider-text">
            <h1 class="slider-title">
              Your favorite wallet which save your every single penny everywhere
            </h1>
            <p class="slider-desc">
              Small change deposit · Easily Cashed · Easily pay your need
            </p>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8">
            <div class="row slider-calculator">
              <div class="col-md-12 text-center sc-title">
                IMME maximize your coin
              </div>
              <div class="col-md-12 text-center sc-desc">
                No more useless coin in your pocket, save your coin to imme wallet now!
              </div>
              <a name="Calculate" id="Calculate"></a>
              <form id="calculator">
                <div class="col-lg-6 col-md-5 col-sm-6 sc-title sc-form">
                  <ol>
                    <li>
                      <p>How much coin that you have every buy on minimarket? </p>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-receh" id="calc-receh" value="200" checked>
                          Rp 200,-
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-receh" id="calc-receh" value="500">
                          Rp 500,-
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-receh" id="calc-receh" value="800">
                          Rp 800,-
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-receh" id="calc-receh" value="1000">
                          Rp 1000,-
                        </label>
                      </div>
                    </li>
                  </ol>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 sc-title sc-form">
                  <ol start="2">
                    <li>
                      <p>How many times you go to minimarket in a day? </p>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-hari" id="calc-hari" value="1" checked>
                          1 times in a day
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-hari" id="calc-hari" value="2">
                          2 times in a day
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-hari" id="calc-hari" value="3">
                          3 times in a day
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="calc-hari" id="calc-hari" value="4">
                          4 times in a day
                        </label>
                      </div>
                    </li>
                  </ol>
                </div>
              </form>

              <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 col-sm-12 hr-sparator">
                <hr>
                <p class=" text-center sc-benefit">Yes! get more benefit just from your coin.</p>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 result">
                <div class="wallet text-center">
                  <p class="wallet-header">
                    In a month you have
                  </p>
                  <span id="coin100"></span>
                  <span id="coin200">30 coins of Rp200,-<br></span>
                  <span id="coin500"></span>
                  <span id="coin1000"></span>
                </div>
              </div>

              <div class="col-lg-6 col-md-6 col-sm-6 text-center result">
                <div class="imme-apps">
                  <div class="inapps-money">
                    <div class="inapps-rp">Rp</div>
                    <div class="inapps-balance" id="imme-balance">6.000</div>
                  </div>
                  <div class="inapps-suggest">Now you can pay food and transfer to your friend using coins.</div>
                </div>
              </div>

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <p class="sc-note">Get free voucher now! <a href="<?php echo base_url("try-imme"); ?>" class="white-standard-link">Let's Try!</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
		</div>
    <a name="TryIMME" id="TryIMME"></a>
    <div class="container-fluid separator-blue">
      <div class="row">
        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 col-sm-12 col-xs-12 text-center test-apps">
          <div class="test-imme">Try imme and get free voucher</div>
          <a href="#" class="btn btn-success white-button" data-toggle="modal" data-target="#downloadModal">DOWNLOAD</a>
        </div>
      </div>
    </div>
    
    <div class="container">
      <div class="row content">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 text-right cont-top">
          <h2>Fast and secure to request money</h2>
          <p>
            With imme you can pay everything 40% faster.<br>
            Get your small change in QRCode voucher, easily split to cash or voucher.<br>
            Say hello to imme, goodbye counting coins.
          </p>
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
          <div class="cd-phone-frame">
            <div class="cd-image-wrapper">
              <video autoplay loop class="video">
                <source src="<?php echo base_url("assets/demo-apps.mp4"); ?>" type="video/mp4">
              </video>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="container-fluid content2">
      <div class="container">
        <div class="row content">
          <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <h3>Easy to use</h3>
            <p class="content-desc">
              Transfer and pay with QR scanner.<br>
              Fast and secure to request money.<br>
              Full access of collecting cash from our vendor.<br><br>
              <b>Just one tap for every needs.</b>
            </p>
          </div>
        
          <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <h3>Security is our main priority</h3>
            <p class="content-desc">
              We keep your money safely.<br><br>
              If you lost your phone, you can easily freeze and unfreeze your money from imme web app.
            </p>
          </div>
        
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <h3>Comfort and Free</h3>
            <p class="content-desc">
              You can withdraw your money at our merchant,
              transfer to your own bank account or your
              friend’s without fee!<br class="hidden-sm"><br>
              Designed for daily payment.
            </p>
          </div>
        </div>
      </div>
    </div>
    <a name="VoteYourCity" id="VoteYourCity"></a>
    <div class="container">
      <div class="row content">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
          <h2>ALL AROUND INDONESIA</h2>
          <p><b>create innovative urban technology, to improve suburban economy</b></p>
          <p>
            Jakarta • Surabaya • Malang • Jogjakarta • Bali • Bogor • Tangerang • Banten • Bandung • Bekasi
          </p>
        </div>
        
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 vote-location">
          <h4>Where's next?</h4>
          <h5>help your city to create innovative chalanges!</h5>
          
          <div id="voteAlert" class="text-center vote-result"<?php if (!$is_support_city) { echo ' style="display:none"'; }?>>
            Yay! 1 vote again for
            <b id="cityVoteValue">
              <?php if (!empty($city)) {
              echo $city;
            } ?>
            </b> or <a href="#VoteAgain" id="vote-again">add new vote</a>
          </div>

          <form class="text-right"<?php if ($is_support_city) { echo ' style="display:none"'; }?> id="voteForm">
            <div class="form-group">
              <select class="form-control" id="vote-city">
                <?php foreach ($citys as $value): ?>
                  <option value="<?php echo $value->city_id; ?>"><?php echo $value->city_name; ?></option>
                <?php endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-default" id="vote-my-city">Vote My City!</button>
          </form>

          <div id="voteLoading" class="loading text-center" style="display:none;"><img src="<?php echo base_url("assets/images/loading-1x.gif"); ?>"></div>
        </div>
      </div>
    </div>
    
    <div class="container-fluid footer">
      <div class="row">
        <div class="col-md-12 text-center">
          <h3>Send email to</h3>
          <form class="form-inline" id="followForm">
            
            <div class="form-group">
              <label for="exampleInputEmail2"></label>
              <input type="email" class="form-control" id="follow-email" placeholder="my@email.com">
            </div>
            <button type="submit" class="btn btn-default white-button">Follow</button>
          </form>

          <div id="followLoading" class="loading text-center" style="display:none;"><img src="<?php echo base_url("assets/images/loading-1x.gif"); ?>" class="img-loading"></div>

          <div id="followMessage" class="text-center" style="display:none;padding:1em;"></div>

          <h4>when it's ready at</h4>
          <ul class="list-inline list-unstyled store">
            <li><img src="<?php echo base_url("assets/images/appstorebutton.png"); ?>"></li>
            <li><img src="<?php echo base_url("assets/images/playstorebutton.png"); ?>"></li>
          </ul>
          <h5 class="apps-privacy">"We don't send spam, we will let you know when our app is ready" Learn our <a href="#" class="white-standard-link">Privacy Policy</a></h5>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="downloadModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="downloadModalLabel">One step closer!</h4>
          </div>
          <div class="modal-body">
            We will send our apk to your email, in alpha stage we couldn't publish our apps to Appstore. Why? <a href="#">Learn More</a>

            <div id="downloadLoading" class="loading text-center" style="display:none;"><img src="<?php echo base_url("assets/images/loading-1x.gif"); ?>"></div>

            <form id="downloadForm" class="form-inline download text-center">
              <div class="form-group">
                <input type="email" class="form-control" id="download-email" placeholder="your@email.com">
              </div>
              <button type="submit" class="btn btn-default">Send apps</button>
            </form>

            <div id="downloadAlert" class="text-center" style="display:none;padding:1em;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default white-button" data-dismiss="modal">Cancel</button>
          </div>
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