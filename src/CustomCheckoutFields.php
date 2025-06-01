<?php

namespace App\CustomWoocheckout;

class CustomCheckoutFields
{
    public static function init()
    {
        add_filter('gettext', [self::class, 'alterCheckoutTitle'], 20, 3);
        add_filter('woocommerce_checkout_fields', [self::class, 'addCustomFields']);
        add_action('woocommerce_before_checkout_billing_form', [self::class, 'displayCustomFields']);
        add_action('woocommerce_checkout_update_order_meta', [self::class, 'saveCustomFields']);
        add_action('wp_footer', [self::class, 'customCheckoutJS']);
    }

    public static function alterCheckoutTitle($translated_text, $text, $domain)
    {
        if ($domain === 'woocommerce' && $text === 'Billing details') {
            return 'Detalhes do pedido';
        }
        return $translated_text;
    }

    public static function addCustomFields($fields)
    {
        $fields['customer_info'] = [
            'customer_person_type' => [
                'type'     => 'select',
                'label'    => 'Tipo de Pessoa',
                'options'  => [
                    ''   => 'Selecione PF ou PJ',
                    'pf' => 'Pessoa Física',
                    'pj' => 'Pessoa Jurídica',
                ],
                'required' => true,
                'class'    => ['form-row-wide', 'custom-field'],
                'priority' => 1,
            ],
            'customer_document' => [
                'label' => 'CPF / CNPJ',
                'placeholder' => 'Digite CPF ou CNPJ',
                'required' => false,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 2,
            ],
            'customer_birthdate' => [
                'label' => 'Data de Nascimento',
                'placeholder' => 'DD/MM/AAAA',
                'required' => false,
                'class' => ['form-row-wide', 'pf-field', 'custom-field'],
                'priority' => 3,
            ],
            'customer_ie' => [
                'label' => 'Inscrição Estadual',
                'placeholder' => 'Digite a IE',
                'required' => false,
                'class' => ['form-row-wide', 'pj-field', 'custom-field'],
                'priority' => 4,
            ],
            'customer_phone' => [
                'label' => 'Telefone',
                'placeholder' => '(XX) XXXXX-XXXX',
                'required' => false,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 5,
            ],
            'customer_postcode' => [
                'label' => 'CEP',
                'placeholder' => '00000-000',
                'required' => true,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 6,
            ],
            'customer_country' => [
                'label' => 'País',
                'type' => 'country',
                'required' => true,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 7,
            ],
            'customer_state' => [
                'label' => 'Estado',
                'type' => 'state',
                'required' => true,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 8,
            ],
            'customer_city' => [
                'label' => 'Cidade',
                'placeholder' => 'Cidade',
                'required' => true,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 9,
            ],
            'customer_neighborhood' => [
                'label' => 'Bairro',
                'placeholder' => 'Bairro',
                'required' => false,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 10,
            ],
            'customer_address_1' => [
                'label' => 'Rua',
                'placeholder' => 'Rua',
                'required' => true,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 11,
            ],
            'customer_number' => [
                'label' => 'Número',
                'placeholder' => 'Número',
                'required' => true,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 12,
            ],
            'customer_address_2' => [
                'label' => 'Complemento',
                'placeholder' => 'Complemento',
                'required' => false,
                'class' => ['form-row-wide', 'custom-field'],
                'priority' => 13,
            ],
            'copy_to_billing' => [
                'label' => 'Usar essas informações para preencher automaticamente os Detalhes de Cobrança',
                'type' => 'checkbox',
                'class' => ['form-row-wide', 'custom-field'],
                'required' => false,
                'priority' => 14,
            ],
        ];
        return $fields;
    }

    public static function displayCustomFields($checkout)
    {
        foreach (WC()->checkout->checkout_fields['customer_info'] as $key => $field) {
            woocommerce_form_field($key, $field, $checkout->get_value($key));
            if ($key === 'copy_to_billing') {
                echo '<h3>Detalhes da Cobrança</h3>';
            }
        }
    }

    public static function saveCustomFields($order_id)
    {
        foreach (WC()->checkout->checkout_fields['customer_info'] as $key => $field) {
            if (isset($_POST[$key])) {
                update_post_meta($order_id, $key, sanitize_text_field($_POST[$key]));
            }
        }
    }

    public static function customCheckoutJS()
    {
        if (!is_checkout()) return;
        ?>
        <script>
            jQuery(function($){
                function togglePersonType() {
                    var personType = $('#customer_person_type').val();
                    if(personType === 'pf') {
                        $('.custom-field').closest('.form-row').show();
                        $('.pf-field').closest('.form-row').show();
                        $('.pj-field').closest('.form-row').hide();
                        $('#customer_document').attr('placeholder', 'Digite seu CPF');
                    } else if(personType === 'pj') {
                        $('.custom-field').closest('.form-row').show();
                        $('.pf-field').closest('.form-row').hide();
                        $('.pj-field').closest('.form-row').show();
                        $('#customer_document').attr('placeholder', 'Digite seu CNPJ');
                    } else {
                        $('.custom-field').closest('.form-row').hide();
                    }
                }

                $('.custom-field').closest('.form-row').hide();
                togglePersonType();

                $('#customer_person_type').on('change', function(){
                    togglePersonType();
                });

                $('#customer_postcode').on('blur', function(){
                    var cep = $(this).val().replace(/\D/g, '');
                    if(cep.length !== 8) return;

                    $.getJSON('https://viacep.com.br/ws/'+cep+'/json/', function(data){
                        if(!("erro" in data)) {
                            $('#customer_state').val(data.uf).trigger('change');
                            $('#customer_city').val(data.localidade);
                            $('#customer_neighborhood').val(data.bairro);
                            $('#customer_address_1').val(data.logradouro);
                        }
                    });
                });

                $('#copy_to_billing').on('change', function(){
                    if($(this).is(':checked')) {
                        var mapFields = {
                            'customer_document': 'billing_cpf',
                            'customer_phone': 'billing_phone',
                            'customer_company': 'billing_company',
                            'customer_postcode': 'billing_postcode',
                            'customer_state': 'billing_state',
                            'customer_city': 'billing_city',
                            'customer_neighborhood': 'billing_neighborhood',
                            'customer_address_1': 'billing_address_1',
                            'customer_number': 'billing_number',
                            'customer_address_2': 'billing_address_2',
                            'customer_person_type': 'billing_person_type',
                        };
                        $.each(mapFields, function(src, dest){
                            var val = $('#'+src).val();
                            if(val !== undefined) {
                                $('#'+dest).val(val).trigger('change');
                            }
                        });
                    }
                });
            });
        </script>
        <?php
    }
}
