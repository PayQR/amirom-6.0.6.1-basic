%%include_language "_local/eshop/pay_drivers/payqr/driver.lng"%%

<!--#set var="settings_form" value="
    <input type="hidden" name="url" value="###_null_###submitter_link###_null_###">
    
    <tr>
        <td>%%payqr_merchant_id%%:</td>
        <td><input type="text" name="payqr_merchant_id" class="field" value="##payqr_merchant_id##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_merchant_secret_key_in%%:</td>
        <td><input type="text" name="payqr_merchant_secret_key_in" class="field" value="##payqr_merchant_secret_key_in##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_merchant_secret_key_out%%:</td>
        <td><input type="text" name="payqr_merchant_secret_key_out" class="field" value="##payqr_merchant_secret_key_out##" size="40"></td>
    </tr>
    ##setvar @payqr_receiver_script_file=getPayQRReceiverScriptFile()##
    <tr>
        <td>%%payqr_hook_handler_url%%:</td>
        <td><input type="text" name="payqr_hook_handler_url" class="field" value="##payqr_receiver_script_file##" size="40" disabled></td>
    </tr>
    ##setvar @payqr_log_file=getPayQRLogFile()##
    <tr>
        <td>%%payqr_log_url%%:</td>
        <td><input type="text" name="payqr_log_url" class="field" value="##payqr_log_file##" size="40" disabled></td>
    </tr>
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td>%%payqr_button_show_on_cart%%:</td>
        <td>
        	<select name="payqr_button_show_on_cart">
        		<option value="yes" ##if(payqr_button_show_on_cart=="yes")##selected##endif##>%%yes%%</option>
        		<option value="no" ##if(payqr_button_show_on_cart=="no")##selected##endif##>%%no%%</option>
        	</select>
        </td>
    </tr>
    ##if(payqr_button_show_on_cart=="yes")##
    <tr>
        <td>%%payqr_cart_button_color%%:</td>
        <td>
        	<select name="payqr_cart_button_color">
        		<option value="default" ##if(payqr_cart_button_color=="default")##selected##endif##>%%default%%</option>
        		<option value="green" ##if(payqr_cart_button_color=="green")##selected##endif##>%%green%%</option>
        		<option value="red" ##if(payqr_cart_button_color=="red")##selected##endif##>%%red%%</option>
        		<option value="blue" ##if(payqr_cart_button_color=="blue")##selected##endif##>%%blue%%</option>
        		<option value="orange" ##if(payqr_cart_button_color=="orange")##selected##endif##>%%orange%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_form%%:</td>
        <td>
        	<select name="payqr_cart_button_form">
        		<option value="default" ##if(payqr_cart_button_form=="default")##selected##endif##>%%default%%</option>
        		<option value="sharp" ##if(payqr_cart_button_form=="sharp")##selected##endif##>%%sharp%%</option>
        		<option value="rude" ##if(payqr_cart_button_form=="rude")##selected##endif##>%%rude%%</option>
        		<option value="soft" ##if(payqr_cart_button_form=="soft")##selected##endif##>%%soft%%</option>
        		<option value="sleek" ##if(payqr_cart_button_form=="sleek")##selected##endif##>%%sleek%%</option>
        		<option value="oval" ##if(payqr_cart_button_form=="oval")##selected##endif##>%%oval%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_shadow%%:</td>
        <td>
        	<select name="payqr_cart_button_shadow">
        		<option value="default" ##if(payqr_cart_button_shadow=="default")##selected##endif##>%%default%%</option>
        		<option value="shadow" ##if(payqr_cart_button_shadow=="shadow")##selected##endif##>%%shadow%%</option>
        		<option value="noshadow" ##if(payqr_cart_button_shadow=="noshadow")##selected##endif##>%%noshadow%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_gradient%%:</td>
        <td>
        	<select name="payqr_cart_button_gradient">
        		<option value="default" ##if(payqr_cart_button_gradient=="default")##selected##endif##>%%default%%</option>
        		<option value="flat" ##if(payqr_cart_button_gradient=="flat")##selected##endif##>%%flat%%</option>
        		<option value="gradient" ##if(payqr_cart_button_gradient=="gradient")##selected##endif##>%%gradient%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_font_trans%%:</td>
        <td>
        	<select name="payqr_cart_button_font_trans">
        		<option value="default" ##if(payqr_cart_button_font_trans=="default")##selected##endif##>%%default%%</option>
        		<option value="small" ##if(payqr_cart_button_font_trans=="small")##selected##endif##>%%small%%</option>
        		<option value="medium" ##if(payqr_cart_button_font_trans=="medium")##selected##endif##>%%medium%%</option>
        		<option value="large" ##if(payqr_cart_button_font_trans=="large")##selected##endif##>%%large%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_font_width%%:</td>
        <td>
        	<select name="payqr_cart_button_font_width">
        		<option value="default" ##if(payqr_cart_button_font_width=="default")##selected##endif##>%%default%%</option>
        		<option value="normal" ##if(payqr_cart_button_font_width=="normal")##selected##endif##>%%normal%%</option>
        		<option value="bold" ##if(payqr_cart_button_font_width=="bold")##selected##endif##>%%bold%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_text_case%%:</td>
        <td>
        	<select name="payqr_cart_button_text_case">
        		<option value="default" ##if(payqr_cart_button_text_case=="default")##selected##endif##>%%default%%</option>
        		<option value="lowercase" ##if(payqr_cart_button_text_case=="lowercase")##selected##endif##>%%lowercase%%</option>
        		<option value="standartcase" ##if(payqr_cart_button_text_case=="standartcase")##selected##endif##>%%standartcase%%</option>
        		<option value="uppercase" ##if(payqr_cart_button_text_case=="uppercase")##selected##endif##>%%uppercase%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_height%%:</td>
        <td>
        	<input type="text" name="payqr_cart_button_height" class="field" value="##if(payqr_cart_button_height=="")##auto##endif####if(payqr_cart_button_height!="")####payqr_cart_button_height####endif##" size="40">
       	</td>
    </tr>
    <tr>
        <td>%%payqr_cart_button_width%%:</td>
        <td>
        	<input type="text" name="payqr_cart_button_width" class="field" value="##if(payqr_cart_button_width=="")##auto##endif####if(payqr_cart_button_width!="")####payqr_cart_button_width####endif##" size="40">
        </td>
    </tr>
    ##endif##
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td>%%payqr_button_show_on_product%%:</td>
        <td>
        	<select name="payqr_button_show_on_product">
        		<option value="yes" ##if(payqr_button_show_on_product=="yes")##selected##endif##>%%yes%%</option>
        		<option value="no" ##if(payqr_button_show_on_product=="no")##selected##endif##>%%no%%</option>
        	</select>
        </td>
    </tr>
    ##if(payqr_button_show_on_product=="yes")##
    <tr>
        <td>%%payqr_product_button_color%%:</td>
        <td>
        	<select name="payqr_product_button_color">
        		<option value="default" ##if(payqr_product_button_color=="default")##selected##endif##>%%default%%</option>
        		<option value="green" ##if(payqr_product_button_color=="green")##selected##endif##>%%green%%</option>
        		<option value="red" ##if(payqr_product_button_color=="red")##selected##endif##>%%red%%</option>
        		<option value="blue" ##if(payqr_product_button_color=="blue")##selected##endif##>%%blue%%</option>
        		<option value="orange" ##if(payqr_product_button_color=="orange")##selected##endif##>%%orange%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_form%%:</td>
        <td>
        	<select name="payqr_product_button_form">
        		<option value="default" ##if(payqr_product_button_form=="default")##selected##endif##>%%default%%</option>
        		<option value="sharp" ##if(payqr_product_button_form=="sharp")##selected##endif##>%%sharp%%</option>
        		<option value="rude" ##if(payqr_product_button_form=="rude")##selected##endif##>%%rude%%</option>
        		<option value="soft" ##if(payqr_product_button_form=="soft")##selected##endif##>%%soft%%</option>
        		<option value="sleek" ##if(payqr_product_button_form=="sleek")##selected##endif##>%%sleek%%</option>
        		<option value="oval" ##if(payqr_product_button_form=="oval")##selected##endif##>%%oval%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_shadow%%:</td>
        <td>
        	<select name="payqr_product_button_shadow">
        		<option value="default" ##if(payqr_product_button_shadow=="default")##selected##endif##>%%default%%</option>
        		<option value="shadow" ##if(payqr_product_button_shadow=="shadow")##selected##endif##>%%shadow%%</option>
        		<option value="noshadow" ##if(payqr_product_button_shadow=="noshadow")##selected##endif##>%%noshadow%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_gradient%%:</td>
        <td>
        	<select name="payqr_product_button_gradient">
        		<option value="default" ##if(payqr_product_button_gradient=="default")##selected##endif##>%%default%%</option>
        		<option value="flat" ##if(payqr_product_button_gradient=="flat")##selected##endif##>%%flat%%</option>
        		<option value="gradient" ##if(payqr_product_button_gradient=="gradient")##selected##endif##>%%gradient%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_font_trans%%:</td>
        <td>
        	<select name="payqr_product_button_font_trans">
        		<option value="default" ##if(payqr_product_button_font_trans=="default")##selected##endif##>%%default%%</option>
        		<option value="small" ##if(payqr_product_button_font_trans=="small")##selected##endif##>%%small%%</option>
        		<option value="medium" ##if(payqr_product_button_font_trans=="medium")##selected##endif##>%%medium%%</option>
        		<option value="large" ##if(payqr_product_button_font_trans=="large")##selected##endif##>%%large%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_font_width%%:</td>
        <td>
        	<select name="payqr_product_button_font_width">
        		<option value="default" ##if(payqr_product_button_font_width=="default")##selected##endif##>%%default%%</option>
        		<option value="normal" ##if(payqr_product_button_font_width=="normal")##selected##endif##>%%normal%%</option>
        		<option value="bold" ##if(payqr_product_button_font_width=="bold")##selected##endif##>%%bold%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_text_case%%:</td>
        <td>
        	<select name="payqr_product_button_text_case">
        		<option value="default" ##if(payqr_product_button_text_case=="default")##selected##endif##>%%default%%</option>
        		<option value="lowercase" ##if(payqr_product_button_text_case=="lowercase")##selected##endif##>%%lowercase%%</option>
        		<option value="standartcase" ##if(payqr_product_button_text_case=="standartcase")##selected##endif##>%%standartcase%%</option>
        		<option value="uppercase" ##if(payqr_product_button_text_case=="uppercase")##selected##endif##>%%uppercase%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_height%%:</td>
        <td>
        	<input type="text" name="payqr_product_button_height" class="field" value="##if(payqr_product_button_height=="")##auto##endif####if(payqr_product_button_height!="")####payqr_product_button_height####endif##" size="40">
        </td>
    </tr>
    <tr>
        <td>%%payqr_product_button_width%%:</td>
        <td>
        	<input type="text" name="payqr_product_button_width" class="field" value="##if(payqr_product_button_width=="")##auto##endif####if(payqr_product_button_width!="")####payqr_product_button_width####endif##" size="40">
        </td>
    </tr>
    ##endif##
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td>%%payqr_button_show_on_category%%:</td>
        <td>
        	<select name="payqr_button_show_on_category">
        		<option value="yes" ##if(payqr_button_show_on_category=="yes")##selected##endif##>%%yes%%</option>
        		<option value="no" ##if(payqr_button_show_on_category=="no")##selected##endif##>%%no%%</option>
        	</select>
        </td>
    </tr>
    ##if(payqr_button_show_on_category=="yes")##
    <tr>
        <td>%%payqr_category_button_color%%:</td>
        <td>
        	<select name="payqr_category_button_color">
        		<option value="default" ##if(payqr_category_button_color=="default")##selected##endif##>%%default%%</option>
        		<option value="green" ##if(payqr_category_button_color=="green")##selected##endif##>%%green%%</option>
        		<option value="red" ##if(payqr_category_button_color=="red")##selected##endif##>%%red%%</option>
        		<option value="blue" ##if(payqr_category_button_color=="blue")##selected##endif##>%%blue%%</option>
        		<option value="orange" ##if(payqr_category_button_color=="orange")##selected##endif##>%%orange%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_form%%:</td>
        <td>
        	<select name="payqr_category_button_form">
        		<option value="default" ##if(payqr_category_button_form=="default")##selected##endif##>%%default%%</option>
        		<option value="sharp" ##if(payqr_category_button_form=="sharp")##selected##endif##>%%sharp%%</option>
        		<option value="rude" ##if(payqr_category_button_form=="rude")##selected##endif##>%%rude%%</option>
        		<option value="soft" ##if(payqr_category_button_form=="soft")##selected##endif##>%%soft%%</option>
        		<option value="sleek" ##if(payqr_category_button_form=="sleek")##selected##endif##>%%sleek%%</option>
        		<option value="oval" ##if(payqr_category_button_form=="oval")##selected##endif##>%%oval%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_shadow%%:</td>
        <td>
        	<select name="payqr_category_button_shadow">
        		<option value="default" ##if(payqr_category_button_shadow=="default")##selected##endif##>%%default%%</option>
        		<option value="shadow" ##if(payqr_category_button_shadow=="shadow")##selected##endif##>%%shadow%%</option>
        		<option value="noshadow" ##if(payqr_category_button_shadow=="noshadow")##selected##endif##>%%noshadow%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_gradient%%:</td>
        <td>
        	<select name="payqr_category_button_gradient">
        		<option value="default" ##if(payqr_category_button_gradient=="default")##selected##endif##>%%default%%</option>
        		<option value="flat" ##if(payqr_category_button_gradient=="flat")##selected##endif##>%%flat%%</option>
        		<option value="gradient" ##if(payqr_category_button_gradient=="gradient")##selected##endif##>%%gradient%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_font_trans%%:</td>
        <td>
        	<select name="payqr_category_button_font_trans">
        		<option value="default" ##if(payqr_category_button_font_trans=="default")##selected##endif##>%%default%%</option>
        		<option value="small" ##if(payqr_category_button_font_trans=="small")##selected##endif##>%%small%%</option>
        		<option value="medium" ##if(payqr_category_button_font_trans=="medium")##selected##endif##>%%medium%%</option>
        		<option value="large" ##if(payqr_category_button_font_trans=="large")##selected##endif##>%%large%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_font_width%%:</td>
        <td>
        	<select name="payqr_category_button_font_width">
        		<option value="default" ##if(payqr_category_button_font_width=="default")##selected##endif##>%%default%%</option>
        		<option value="normal" ##if(payqr_category_button_font_width=="normal")##selected##endif##>%%normal%%</option>
        		<option value="bold" ##if(payqr_category_button_font_width=="bold")##selected##endif##>%%bold%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_text_case%%:</td>
        <td>
        	<select name="payqr_category_button_text_case">
        		<option value="default" ##if(payqr_category_button_text_case=="default")##selected##endif##>%%default%%</option>
        		<option value="lowercase" ##if(payqr_category_button_text_case=="lowercase")##selected##endif##>%%lowercase%%</option>
        		<option value="standartcase" ##if(payqr_category_button_text_case=="standartcase")##selected##endif##>%%standartcase%%</option>
        		<option value="uppercase" ##if(payqr_category_button_text_case=="uppercase")##selected##endif##>%%uppercase%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_height%%:</td>
        <td>
        	<input type="text" name="payqr_category_button_height" class="field" value="##if(payqr_category_button_height=="")##auto##endif####if(payqr_category_button_height!="")####payqr_category_button_height####endif##" size="40">
        </td>
    </tr>
    <tr>
        <td>%%payqr_category_button_width%%:</td>
        <td>
        	<input type="text" name="payqr_category_button_width" class="field" value="##if(payqr_category_button_width=="")##auto##endif####if(payqr_category_button_width!="")####payqr_category_button_width####endif##" size="40">
        </td>
    </tr>
    ##endif##
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td>%%payqr_status_creatted%%:</td>
        <td>
            <select name="payqr_status_creatted">
                <option value="draft" ##if(payqr_status_creatted=="draft")##selected##endif##>Запрос</option>
                <option value="accepted" ##if(payqr_status_creatted=="accepted")##selected##endif##>Оплачен</option>
                <option value="cancelled" ##if(payqr_status_creatted=="cancelled")##selected##endif##>Отменен</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_status_paid%%:</td>
        <td>
            <select name="payqr_status_paid">
                <option value="draft" ##if(payqr_status_paid=="draft")##selected##endif##>Запрос</option>
                <option value="accepted" ##if(payqr_status_paid=="accepted")##selected##endif##>Оплачен</option>
                <option value="cancelled" ##if(payqr_status_paid=="cancelled")##selected##endif##>Отменен</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_status_canceled%%:</td>
        <td>
            <select name="payqr_status_canceled">
                <option value="draft" ##if(payqr_status_canceled=="draft")##selected##endif##>Запрос</option>
                <option value="accepted" ##if(payqr_status_canceled=="accepted")##selected##endif##>Оплачен</option>
                <option value="cancelled" ##if(payqr_status_canceled=="cancelled")##selected##endif##>Отменен</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_require_firstname%%:</td>
        <td>
        	<select name="payqr_require_firstname">
                    <option value="default" ##if(payqr_require_firstname=="default")##selected##endif##>%%default%%</option>
                    <option value="deny" ##if(payqr_require_firstname=="deny")##selected##endif##>%%deny%%</option>
                    <option value="required" ##if(payqr_require_firstname=="required")##selected##endif##>%%required%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_require_lastname%%:</td>
        <td>
        	<select name="payqr_require_lastname">
                <option value="default" ##if(payqr_require_lastname=="default")##selected##endif##>%%default%%</option>
        		<option value="deny" ##if(payqr_require_lastname=="deny")##selected##endif##>%%deny%%</option>
        		<option value="required" ##if(payqr_require_lastname=="required")##selected##endif##>%%required%%</option>
        	</select>
        </td>
    </tr>
    <tr style="display:none;">
        <td>%%payqr_require_middlename%%:</td>
        <td>
        	<select name="payqr_require_middlename">
                <option value="default" ##if(payqr_require_middlename=="default")##selected##endif##>%%default%%</option>
        		<option value="deny" ##if(payqr_require_middlename=="deny")##selected##endif## selected>%%deny%%</option>
        		<option value="required" ##if(payqr_require_middlename=="required")####endif##>%%required%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_require_phone%%:</td>
        <td>
        	<select name="payqr_require_phone">
                <option value="default" ##if(payqr_require_phone=="default")##selected##endif##>%%default%%</option>
        		<option value="deny" ##if(payqr_require_phone=="deny")##selected##endif##>%%deny%%</option>
        		<option value="required" ##if(payqr_require_phone=="required")##selected##endif##>%%required%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_require_email%%:</td>
        <td>
        	<select name="payqr_require_email" disabled>
                <option value="default" ##if(payqr_require_email=="default")##selected##endif##>%%default%%</option>
        		<option value="deny" ##if(payqr_require_email=="deny")##selected##endif##>%%deny%%</option>
        		<option value="required" ##if(payqr_require_email=="required")##selected##endif## selected>%%required%%</option>
        	</select>
            <input type="hidden" name="payqr_require_email" value="required" />
        </td>
    </tr>
    <tr>
        <td>%%payqr_require_delivery%%:</td>
        <td>
        	<select name="payqr_require_delivery">
                <option value="default" ##if(payqr_require_delivery=="default")##selected##endif##>%%default%%</option>
        		<option value="deny" ##if(payqr_require_delivery=="deny")##selected##endif##>%%deny%%</option>
        		<option value="required" ##if(payqr_require_delivery=="required")##selected##endif##>%%required%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_require_deliverycases%%:</td>
        <td>
        	<select name="payqr_require_deliverycases">
                <option value="default" ##if(payqr_require_deliverycases=="default")##selected##endif##>%%default%%</option>
        		<option value="deny" ##if(payqr_require_deliverycases=="deny")##selected##endif##>%%deny%%</option>
        		<option value="required" ##if(payqr_require_deliverycases=="required")##selected##endif##>%%required%%</option>
        	</select>
        </td>
    </tr>
    <tr>
        <td>%%payqr_require_pickpoints%%:</td>
        <td>
        	<select name="payqr_require_pickpoints" disabled>
                <option value="default" ##if(payqr_require_pickpoints=="default")##selected##endif##>%%default%%</option>
        		<option value="deny" ##if(payqr_require_pickpoints=="deny")##selected##endif## selected>%%deny%%</option>
        		<option value="required" ##if(payqr_require_pickpoints=="required")##selected##endif##>%%required%%</option>
        	</select>
        </td>
        <input type="hidden" name="payqr_require_pickpoints" value="deny" />
    </tr>
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td>%%payqr_ocreating_message_text%%:</td>
        <td><input type="text" name="payqr_ocreating_message_text" class="field" value="##payqr_ocreating_message_text##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_ocreating_message_imageurl%%:</td>
        <td><input type="text" name="payqr_ocreating_message_imageurl" class="field" value="##payqr_ocreating_message_imageurl##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_ocreating_message_url%%:</td>
        <td><input type="text" name="payqr_ocreating_message_url" class="field" value="##payqr_ocreating_message_url##" size="40"></td>
    </tr>
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td>%%payqr_paid_message_text%%:</td>
        <td><input type="text" name="payqr_paid_message_text" class="field" value="##payqr_paid_message_text##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_paid_message_imageurl%%:</td>
        <td><input type="text" name="payqr_paid_message_imageurl" class="field" value="##payqr_paid_message_imageurl##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_paid_message_url%%:</td>
        <td><input type="text" name="payqr_paid_message_url" class="field" value="##payqr_paid_message_url##" size="40"></td>
    </tr>
    <tr><td colspan="2"><hr></td></tr>
    <tr>
        <td>%%payqr_reverted_message_text%%:</td>
        <td><input type="text" name="payqr_reverted_message_text" class="field" value="##payqr_reverted_message_text##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_reverted_message_imageurl%%:</td>
        <td><input type="text" name="payqr_reverted_message_imageurl" class="field" value="##payqr_reverted_message_imageurl##" size="40"></td>
    </tr>
    <tr>
        <td>%%payqr_reverted_message_url%%:</td>
        <td><input type="text" name="payqr_reverted_message_url" class="field" value="##payqr_reverted_message_url##" size="40"></td>
    </tr>
    
    
    <script type="text/javascript">
            AMI.$(document).ready(function(){
                AMI.$('form[name=pay_drivers_form] table tr:nth-child(4)').hide();
                AMI.$('form[name=pay_drivers_form] table tr:nth-child(5)').hide();
                AMI.$('form[name=pay_drivers_form] table tr:nth-child(6)').hide();
            });
    </script> 
"-->

<!--#set var="checkout_form" value="
    <form name="paymentformexample" action="##process_url##" method="POST">
    <input type="hidden" name="amount" value="##amount##">
    <input type="hidden" name="description" value="##description##">
    <input type="hidden" name="order" value="##order##">
    ##hiddens##
    <input type="submit" name="sbmt" class="btn" value="%%driver_button_caption%%" ##disabled##>
    </form>
"-->

<!--#set var="pay_form" value="
    <form name="paymentform" action="##url##" method="post">
    <input type="hidden" name="item_number" value="##order##">
    <input type="hidden" name="status" value="ok">
    ##hiddens##
    </form>
    <script type="text/javascript">
            document.paymentform.submit();
    </script> 
"-->
