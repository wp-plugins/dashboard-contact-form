jQuery( document ).ready(function($) {
		$("#cdw_content").val(SCF.message);
		$( "#cdw-support" ).submit(function(event) {
			event.preventDefault();
			$( ".scf-ajax").show();
			 $(".formmessage p").html(SCF.sending);

			var name = $("#cdw_author").val();
			var title = $("#cdw_title").val();
			var message = $("#cdw_content").val();
			var from = $("#cdw_email").val();
			if(message == "" || message == SCF.message || title == "") { 
				$(".formmessage p").html(SCF.invalid);
				if (message == "" || message == SCF.message) $("#cdw_content").css({"border-color": "red"});
				if (title == "") $("#cdw_title").css({"border-color": "red"});
				$( ".scf-ajax").hide();			
			}	
			else {
			var data =  { action :	'cdw_send_message', 'author_name': name, 'author_email': from, 'mail_title': title, 'mail_content': message};
			$("#cdw_content").removeAttr('style');
			$("#cdw_title").removeAttr('style');
			var posting = $.post( ajaxurl, data,function(response){
				$(".formmessage p").html(response);
			})
			.always(function (){
				$( ".scf-ajax").hide();
				setTimeout( function(){ 
					$(".formmessage p").fadeOut(500,function(){
					$(this).html("");
					$(this).show();});},3000 ); 
			}).done(function(){
				$("#cdw_title").val("");
				$("#cdw_content").val("");
			});}
			
			return false;
	});	
});