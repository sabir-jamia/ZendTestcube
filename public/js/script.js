/* ========================================================================
 * Author : Divesh
 * Module : Application
 * Date	  : 27-06-2014
 * site   : zendtestcube.com
 * ======================================================================== */

//////////////// Sidebar navigation scripts //////////////////////////////////

function removePageCurrent()
            {
                $('#icoDash').removeClass("page-current");
                $('#icoCategory').removeClass("page-current");
                $('#icoQuestions').removeClass("page-current");
                $('#icoTests').removeClass("page-current");
                $('#icoResults').removeClass("page-current");
                $('#icoCertificates').removeClass("page-current");
                $('#icoUsers').removeClass("page-current");

                $('#lnkDash').removeClass("link-current");
                $('#lnkCategory').removeClass("link-current");
                $('#lnkQuestions').removeClass("link-current");
                $('#lnkTests').removeClass("link-current");
                $('#lnkResults').removeClass("link-current");
                $('#lnkCertificates').removeClass("link-current");
                $('#lnkUsers').removeClass("link-current");
            }
            $('#ancrDash').click(function()
            {
                removePageCurrent();
                $('#icoDash').addClass("page-current");
                $('#lnkDash').addClass("link-current");
            });
            
            $('#ancrCategory').click(function()
            {
                removePageCurrent();
                $('#icoCategory').addClass("page-current");
                $('#lnkCategory').addClass("link-current");
            });

            $('#ancrQuestions').click(function()
            {
                removePageCurrent();
                $('#icoQuestions').addClass("page-current");
                $('#lnkQuestions').addClass("link-current");
            });

            $('#ancrTests').click(function()
            {
                removePageCurrent();
                $('#icoTests').addClass("page-current");
                $('#lnkTests').addClass("link-current");
            });

            $('#ancrCertificates').click(function()
            {
                removePageCurrent();
                $('#icoCertificates').addClass("page-current");
                $('#lnkCertificates').addClass("link-current");
            });

            $('#ancrResulsts').click(function()
            {
                removePageCurrent();
                $('#icoResults').addClass("page-current");
                $('#lnkResults').addClass("link-current");
            });

            $('#ancrUsers').click(function()
            {
                removePageCurrent();
                $('#icoUsers').addClass("page-current");
                $('#lnkUsers').addClass("link-current");
            });
            function addPageCurrent(element)
            {
                var el="#ico"+element;
                $(el).addClass("page-current");
            }
    
            function addClassCurrent(element)
            {
                var el="#ico"+element;
                $(el).addClass("current");
            }

            function removeClassCurrent(element)
            {
                var el="#ico"+element;
                $(el).removeClass("current");
            }
            $('#footer').css("padding-top","30px");

/* ========================================================================
 * END
 * ======================================================================== */

	