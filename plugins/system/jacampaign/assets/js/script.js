(function($){	
	$(document).ready(function(e){
		var duration = $('#popupModal').data('duration')*1000;

	    setTimeout(function(){  //60 sec
	       try {
		        var diffDate = new Date().getTime() - (localStorage.getItem('showedModal') ? parseInt(localStorage.getItem('showedModal')) : parseInt(10));
		        if(diffDate > 86400000){   //check showed
		            $('#popupModal').modal();
		        }
	        } catch(e){
	        }
	    }, duration);
	    $('#popupModal').on('shown.bs.modal', function (e) {
		  	try {
	            localStorage.setItem('showedModal', new Date().getTime());  //set showed
	        } catch (e) {
	        }
		})
	});
})(jQuery);