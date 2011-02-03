
$(document).ready(function()
{
	$('.changeLink').parent().parent().next().hide();	
	$('.changeLink').click(function() {
		$(this).parent().parent().next().toggle();
		$(this).text($(this).text() == "[Change]" ? "[Hide]" : "[Change]");
		return false;
	})
	
	$('#showTestLink').click(function() {
		$('.hideFromTest').show();
		$('#showTestLink').hide();
		$('#hideTestLink').show();
		return false;
	})
	
	$('#hideTestLink').click(function() {
		$('.hideFromTest').hide();
		$('#showTestLink').show();
		$('#hideTestLink').hide();
		return false;
	})
	
	$('#uploaddraft').hide();	
	$('#uploaddraftLink').click(function() {
		$('#uploaddraft').toggle();
		$(this).text($(this).text() == "[Upload New]" ? "[Hide]" : "[Upload New]");
		return false; 
	})
	
	$('#uploadsolution').hide();	
	$('#uploadsolutionLink').click(function() {
		$('#uploadsolution').toggle();
		$(this).text($(this).text() == "[Upload New]" ? "[Hide]" : "[Upload New]");
		return false; 
	})
	
	$('#uploadmisc').hide();	
	$('#uploadmiscLink').click(function() {
		$('#uploadmisc').toggle();
		$(this).text($(this).text() == "[Upload New]" ? "[Hide]" : "[Upload New]");
		return false; 
	})
	
	$('.fileInfoOld').hide();
	$('#toggleFiles').click(function() {
		$('.fileInfoOld').toggle();
		
		if ($('#toggleFiles').text() == "Show Older Files") {
			$('#toggleFiles').text("Hide Older Files");
			$('td.fileInfoLatest').css('padding-top', '1em');
			$('div.fileInfo').css('padding-top', '0em');
		} else {
			$(this).text("Show Older Files");
			$('td.fileInfoLatest').css('padding-top', '.25em');
			$('div.fileInfo').css('padding-top', '.75em');
		}

		return false;
	})
	
	$('#showPage').click(function() {
		$('.hideFromLurkerTester').show();
		$('#showPageLine').hide();
		return false;
	})
	
	$('.description').parent().next().hide();
	$('.description').click(function() {
		$('.description').parent().next().toggle();
		return false;
	})
	
	$(".tablesorter").tablesorter();
	
	$('textarea').one('keyup',function() {
		$('BODY').attr('onbeforeunload',"return 'Leaving this page will cause any unsaved data to be lost.';");
	});

	$('.okSubmit').click(function() {
		$('BODY').removeAttr('onbeforeunload');
	});
})