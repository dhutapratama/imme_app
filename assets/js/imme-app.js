$(function() {
  jcf.replaceAll();

  // Anchor smooth scrolling
  $('a').click(function(){
      $('html, body').animate({
          scrollTop: $( $(this).attr('href') ).offset().top
      }, 1000);
      return false;
  });

  // Form overide
  $( "form" ).submit(function( event ) {
    event.preventDefault();
  });

  // Coin
  $('#calculator input').on('change', function() {
    var coin100, coin200, coin500, coin1000, money, resultCoin100, resultCoin200, resultCoin500, resultCoin1000, resultMoney;
    if ($('input[name=calc-receh]:checked', '#calculator').val() == "200") {
      coin100 = 0;
      coin200 = 1;
      coin500 = 0;
      coin1000 = 0;
      money = 200;
    } else if ($('input[name=calc-receh]:checked', '#calculator').val() == "500") {
      coin100 = 0;
      coin200 = 0;
      coin500 = 1;
      coin1000 = 0;
      money = 500;
    } else if ($('input[name=calc-receh]:checked', '#calculator').val() == "800") {
      coin100 = 1;
      coin200 = 1;
      coin500 = 1;
      coin1000 = 0;
      money = 800;
    } else if ($('input[name=calc-receh]:checked', '#calculator').val() == "1000") {
      coin100 = 0;
      coin200 = 0;
      coin500 = 0;
      coin1000 = 1;
      money = 1000;
    };

    if ($('input[name=calc-hari]:checked', '#calculator').val() == "1") {
      resultCoin100 = coin100 * 1 * 30;
      resultCoin200 = coin200 * 1 * 30;
      resultCoin500 = coin500 * 1 * 30;
      resultCoin1000 = coin1000 * 1 * 30;
      resultMoney = money * 1 * 30;
    } else if ($('input[name=calc-hari]:checked', '#calculator').val() == "2") {
      resultCoin100 = coin100 * 2 * 30;
      resultCoin200 = coin200 * 2 * 30;
      resultCoin500 = coin500 * 2 * 30;
      resultCoin1000 = coin1000 * 2 * 30;
      resultMoney = money * 2 * 30;
    } else if ($('input[name=calc-hari]:checked', '#calculator').val() == "3") {
      resultCoin100 = coin100 * 3 * 30;
      resultCoin200 = coin200 * 3 * 30;
      resultCoin500 = coin500 * 3 * 30;
      resultCoin1000 = coin1000 * 3 * 30;
      resultMoney = money * 3 * 30;
    } else if ($('input[name=calc-hari]:checked', '#calculator').val() == "4") {
      resultCoin100 = coin100 * 4 * 30;
      resultCoin200 = coin200 * 4 * 30;
      resultCoin500 = coin500 * 4 * 30;
      resultCoin1000 = coin1000 * 4 * 30;
      resultMoney = money * 4 * 30;
    };

    // Reset format
    $("#coin100").html("");
    $("#coin200").html("");
    $("#coin500").html("");
    $("#coin1000").html("");

    if (resultCoin100 != 0) {
      $("#coin100").html("30 coins of Rp100,-<br>");
    };
    if (resultCoin200 != 0) {
      $("#coin200").html("30 coins of Rp200,-<br>");
    };
    if (resultCoin500 != 0) {
      $("#coin500").html("30 coins of Rp500,-<br>");
    };
    if (resultCoin1000 != 0) {
      $("#coin1000").html("30 coins of Rp1000,-<br>");
    };

    $("#imme-balance").html(new Intl.NumberFormat('de-DE', { minimumFractionDigits: 0 }).format(resultMoney));

  });

  var domain = "app", last_url;
  $("#downloadForm").submit(function(){
    $("#downloadForm").toggle();
    $("#downloadLoading").toggle();
    var api_url = "http://imme."+domain+"/traction/download";

    $.ajax({ type: 'POST', url: api_url, data: 
    {
      email: $("#download-email").val()
    },

    xhrFields: { withCredentials: true },
      
    success: function(data, textStatus ){
      if(data.error == false) {
        console.log("Email saved");
        $("#downloadLoading").toggle();
        $("#downloadAlert").toggle();
        $("#downloadAlert").html(data.message);
      } else {
        $("#downloadLoading").toggle();
        $("#downloadForm").toggle();
        $("#downloadAlert").toggle();
        $("#downloadAlert").html(data.message);
      }
    },
      error: function(xhr, textStatus, errorThrown){ alert("network error"); }
    });
  });

  $("#voteForm").submit(function(){
    $("#voteForm").toggle();
    $("#voteLoading").toggle();
    var api_url = "http://imme."+domain+"/traction/support_city";
    last_url = api_url;

    $.ajax({ type: 'POST', url: api_url, data: 
    {
      city_id: $("#vote-city").val()
    },

    xhrFields: { withCredentials: true },
      
    success: function(data, textStatus ){
      if(data.error == false) {
        console.log("Vote saved");
        $("#voteLoading").toggle();
        $("#voteAlert").toggle();
        $("#cityVoteValue").html(data.message);
      } else {
        if (data.get == "email") {
          $('#emailModal').modal('toggle');
        } else {
          console.log("Vote gagal");
          $("#voteLoading").toggle();
          $("#voteForm").toggle();
          $("#voteAlert").toggle();
          $("#voteAlert").html(data.message);
        }
      }
    },
      error: function(xhr, textStatus, errorThrown){ alert("network error"); }
    });
  });

  $("#vote-again").mousedown(function(){
    $("#voteForm").toggle();
    $("#voteAlert").toggle();
  });

  $("#emailForm").submit(function(){
    $("#emailForm").toggle();
    $("#emailLoading").toggle();

    $.ajax({ type: 'POST', url: last_url, data: 
    {
      city_id: $("#vote-city").val(),
      question: $("#notReady-question").val(),
      email: $("#email-email").val()
    },

    xhrFields: { withCredentials: true },
      
    success: function(data, textStatus ){
      if(data.error == false) {
        if (last_url == "http://imme."+domain+"/traction/support_city") {
          $("#voteLoading").toggle();
          $("#voteAlert").toggle();
          $("#cityVoteValue").html(data.message);
        } else {
          $("#notReadyLoading").toggle();
          $("#notReadyMessage").toggle();
          $("#notReadyMessage").html(data.message);
        }
        $("#emailForm").toggle();
        $("#emailLoading").toggle();
        $('#emailModal').modal('toggle');
      } else {
        console.log("Vote gagal");
        $("#emailLoading").toggle();
        $("#emailForm").toggle();
        $("#emailMessage").toggle();
        $("#emailMessage").html(data.message);
      }
    },
      error: function(xhr, textStatus, errorThrown){ alert("network error"); }
    });
  });

  $("#followForm").submit(function(){
    $("#followForm").toggle();
    $("#followLoading").toggle();
    var api_url = "http://imme."+domain+"/traction/follow";

    $.ajax({ type: 'POST', url: api_url, data: 
    {
      email: $("#follow-email").val()
    },

    xhrFields: { withCredentials: true },
      
    success: function(data, textStatus ){
      $("#followLoading").toggle();
      if(data.error == false) {
        console.log("Email saved");
        $("#followMessage").toggle();
        $("#followMessage").html(data.message);
      } else {
        $("#followForm").toggle();
        $("#followMessage").toggle();
        $("#followMessage").html(data.message);
      }
    },
      error: function(xhr, textStatus, errorThrown){ alert("network error"); }
    });
  });

  $("#notReadyForm").submit(function(){
    $("#notReadyForm").toggle();
    $("#notReadyLoading").toggle();
    var api_url = "http://imme."+domain+"/traction/question";
    last_url = api_url;

    $.ajax({ type: 'POST', url: api_url, data: 
    {
      question: $("#notReady-question").val()
    },

    xhrFields: { withCredentials: true },
      
    success: function(data, textStatus ){
      if(data.error == false) {
        $("#notReadyLoading").toggle();
        $("#notReadyMessage").toggle();
        $("#notReadyMessage").html(data.message);
      } else {
        if (data.get == "email") {
          $('#emailModal').modal('toggle');
        } else {
          $("#notReadyLoading").toggle();
          $("#notReadyForm").toggle();
          $("#notReadyMessage").toggle();
          $("#notReadyMessage").html(data.message);
        }
      }
    },
      error: function(xhr, textStatus, errorThrown){ alert("network error"); }
    });
  });
});