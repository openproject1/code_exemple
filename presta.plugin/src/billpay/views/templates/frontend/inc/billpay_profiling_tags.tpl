{**
 * Billpay
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer versions in the future.
 *
 * @category  Payment
 * @package   Billpay Prestashop module
 * @author    Billpay GmbH ( support@billpay.de )
 * @author    Catalin Vancea ( catalin.vancea@billpay.de )
 * @copyright Copyright 2012 Billpay GmbH
 * @license   Commercial
 * @link      https://www.billpay.de/
 *}

{if $smarty.session.billpayProfilingTagCount == 1 || !$smarty.session.billpayProfilingTagCount}
    <div id="billpay profiling tag" style="clear: both; float: left; display: none;">
        <p style="background: url(https://cdntm.billpay.de/fp/clear.png?org_id=ulk99l7b&session_id={$smarty.session.md5id}&m=1)"></p>

        <img src="https://cdntm.billpay.de/fp/clear.png?org_id=ulk99l7b&session_id={$smarty.session.md5id}&m=2" alt=""/>

        <script type="text/javascript" src="https://cdntm.billpay.de/fp/check.js?org_id=ulk99l7b&session_id={$smarty.session.md5id}"></script>

        <object type="application/x-shockwave-flash"
            data="https://cdntm.billpay.de/fp/fp.swf?org_id=ulk99l7b&session_id={$smarty.session.md5id}"
            width="1" height="1" id="billpay_profiling_tag">
            <param name="movie" value="https://cdntm.billpay.de/fp/fp.swf?org_id=ulk99l7b&session_id={$smarty.session.md5id}" />
        </object>
        <div></div>
    </div>
{/if}