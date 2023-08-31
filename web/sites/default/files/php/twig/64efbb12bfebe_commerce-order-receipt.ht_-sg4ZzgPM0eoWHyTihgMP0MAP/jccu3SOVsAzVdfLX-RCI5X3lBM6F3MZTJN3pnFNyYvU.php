<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/contrib/commerce/modules/order/templates/commerce-order-receipt.html.twig */
class __TwigTemplate_4d054c1cbd09a0264dd4b3ce379a4d7e extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'order_items' => [$this, 'block_order_items'],
            'shipping_information' => [$this, 'block_shipping_information'],
            'billing_information' => [$this, 'block_billing_information'],
            'payment_method' => [$this, 'block_payment_method'],
            'additional_information' => [$this, 'block_additional_information'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 23
        echo "<table style=\"margin: 15px auto 0 auto; max-width: 768px; font-family: arial,sans-serif\">
  <tbody>
  <tr>
    <td>
      <table style=\"margin-left: auto; margin-right: auto; max-width: 768px; text-align: center;\">
        <tbody>
        <tr>
          <td>
            <a href=\"";
        // line 31
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getUrl("<front>"));
        echo "\" style=\"color: #0e69be; text-decoration: none; font-weight: bold; margin-top: 15px;\">";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order_entity"] ?? null), "getStore", [], "any", false, false, true, 31), "label", [], "any", false, false, true, 31), 31, $this->source), "html", null, true);
        echo "</a>
          </td>
        </tr>
        </tbody>
      </table>
      <table style=\"text-align: center; min-width: 450px; margin: 5px auto 0 auto; border: 1px solid #cccccc; border-radius: 5px; padding: 40px 30px 30px 30px;\">
        <tbody>
        <tr>
          <td style=\"font-size: 30px; padding-bottom: 30px\">";
        // line 39
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Order Confirmation"));
        echo "</td>
        </tr>
        <tr>
          <td style=\"font-weight: bold; padding-top:15px; padding-bottom: 15px; text-align: left; border-top: 1px solid #cccccc; border-bottom: 1px solid #cccccc\">
            ";
        // line 43
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Order #@number details:", ["@number" => twig_get_attribute($this->env, $this->source, ($context["order_entity"] ?? null), "getOrderNumber", [], "any", false, false, true, 43)]));
        echo "
          </td>
        </tr>
        <tr>
          <td>
            ";
        // line 48
        $this->displayBlock('order_items', $context, $blocks);
        // line 65
        echo "          </td>
        </tr>
        <tr>
          <td>
            ";
        // line 69
        if ((($context["billing_information"] ?? null) || ($context["shipping_information"] ?? null))) {
            // line 70
            echo "            <table style=\"width: 100%; padding-top:15px; padding-bottom: 15px; text-align: left; border-top: 1px solid #cccccc; border-bottom: 1px solid #cccccc\">
              <tbody>
              <tr>
                ";
            // line 73
            if (($context["shipping_information"] ?? null)) {
                // line 74
                echo "                  <td style=\"padding-top: 5px; font-weight: bold;\">";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Shipping Information"));
                echo "</td>
                ";
            }
            // line 76
            echo "                ";
            if (($context["billing_information"] ?? null)) {
                // line 77
                echo "                  <td style=\"padding-top: 5px; font-weight: bold;\">";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Billing Information"));
                echo "</td>
                ";
            }
            // line 79
            echo "              </tr>
              <tr>
                ";
            // line 81
            if (($context["shipping_information"] ?? null)) {
                // line 82
                echo "                  <td>
                    ";
                // line 83
                $this->displayBlock('shipping_information', $context, $blocks);
                // line 86
                echo "                  </td>
                ";
            }
            // line 88
            echo "                ";
            if (($context["billing_information"] ?? null)) {
                // line 89
                echo "                  <td>
                    ";
                // line 90
                $this->displayBlock('billing_information', $context, $blocks);
                // line 93
                echo "                  </td>
                ";
            }
            // line 95
            echo "              </tr>
              ";
            // line 96
            if (($context["payment_method"] ?? null)) {
                // line 97
                echo "                <tr>
                  <td style=\"font-weight: bold; margin-top: 10px;\">";
                // line 98
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Payment Method"));
                echo "</td>
                </tr>
                <tr>
                  <td>
                    ";
                // line 102
                $this->displayBlock('payment_method', $context, $blocks);
                // line 105
                echo "                  </td>
                </tr>
              ";
            }
            // line 108
            echo "              </tbody>
            </table>
            ";
        }
        // line 111
        echo "          </td>
        </tr>
        <tr>
          <td>
            <p style=\"margin-bottom: 0;\">
              ";
        // line 116
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Subtotal: @subtotal", ["@subtotal" => $this->extensions['Drupal\commerce_price\TwigExtension\PriceTwigExtension']->formatPrice($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["totals"] ?? null), "subtotal", [], "any", false, false, true, 116), 116, $this->source))]));
        echo "
            </p>
          </td>
        </tr>
        ";
        // line 120
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["totals"] ?? null), "adjustments", [], "any", false, false, true, 120));
        foreach ($context['_seq'] as $context["_key"] => $context["adjustment"]) {
            // line 121
            echo "        <tr>
          <td>
            <p style=\"margin-bottom: 0;\">
              ";
            // line 124
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["adjustment"], "label", [], "any", false, false, true, 124), 124, $this->source), "html", null, true);
            echo ": ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\commerce_price\TwigExtension\PriceTwigExtension']->formatPrice($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["adjustment"], "total", [], "any", false, false, true, 124), 124, $this->source)), "html", null, true);
            echo "
            </p>
          </td>
        </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['adjustment'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 129
        echo "        <tr>
          <td>
            <p style=\"font-size: 24px; padding-top: 15px; padding-bottom: 5px;\">
              ";
        // line 132
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Order Total: @total", ["@total" => $this->extensions['Drupal\commerce_price\TwigExtension\PriceTwigExtension']->formatPrice($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["order_entity"] ?? null), "getTotalPrice", [], "any", false, false, true, 132), 132, $this->source))]));
        echo "
            </p>
          </td>
        </tr>
        <tr>
          <td>
            ";
        // line 138
        $this->displayBlock('additional_information', $context, $blocks);
        // line 141
        echo "          </td>
        </tr>
        </tbody>
      </table>
    </td>
  </tr>
  </tbody>
</table>
";
    }

    // line 48
    public function block_order_items($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 49
        echo "            <table style=\"padding-top: 15px; padding-bottom:15px; width: 100%\">
              <tbody style=\"text-align: left;\">
              ";
        // line 51
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["order_entity"] ?? null), "getItems", [], "any", false, false, true, 51));
        foreach ($context['_seq'] as $context["_key"] => $context["order_item"]) {
            // line 52
            echo "              <tr>
                <td>
                  ";
            // line 54
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, twig_number_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["order_item"], "getQuantity", [], "any", false, false, true, 54), 54, $this->source)), "html", null, true);
            echo " x
                </td>
                <td>
                  <span>";
            // line 57
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["order_item"], "label", [], "any", false, false, true, 57), 57, $this->source), "html", null, true);
            echo "</span>
                  <span style=\"float: right;\">";
            // line 58
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\commerce_price\TwigExtension\PriceTwigExtension']->formatPrice($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["order_item"], "getTotalPrice", [], "any", false, false, true, 58), 58, $this->source)), "html", null, true);
            echo "</span>
                </td>
              </tr>
              ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['order_item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 62
        echo "              </tbody>
            </table>
            ";
    }

    // line 83
    public function block_shipping_information($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 84
        echo "                      ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["shipping_information"] ?? null), 84, $this->source), "html", null, true);
        echo "
                    ";
    }

    // line 90
    public function block_billing_information($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 91
        echo "                      ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["billing_information"] ?? null), 91, $this->source), "html", null, true);
        echo "
                    ";
    }

    // line 102
    public function block_payment_method($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 103
        echo "                      ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["payment_method"] ?? null), 103, $this->source), "html", null, true);
        echo "
                    ";
    }

    // line 138
    public function block_additional_information($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 139
        echo "              ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Thank you for your order!"));
        echo "
            ";
    }

    public function getTemplateName()
    {
        return "modules/contrib/commerce/modules/order/templates/commerce-order-receipt.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  311 => 139,  307 => 138,  300 => 103,  296 => 102,  289 => 91,  285 => 90,  278 => 84,  274 => 83,  268 => 62,  258 => 58,  254 => 57,  248 => 54,  244 => 52,  240 => 51,  236 => 49,  232 => 48,  220 => 141,  218 => 138,  209 => 132,  204 => 129,  191 => 124,  186 => 121,  182 => 120,  175 => 116,  168 => 111,  163 => 108,  158 => 105,  156 => 102,  149 => 98,  146 => 97,  144 => 96,  141 => 95,  137 => 93,  135 => 90,  132 => 89,  129 => 88,  125 => 86,  123 => 83,  120 => 82,  118 => 81,  114 => 79,  108 => 77,  105 => 76,  99 => 74,  97 => 73,  92 => 70,  90 => 69,  84 => 65,  82 => 48,  74 => 43,  67 => 39,  54 => 31,  44 => 23,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/commerce/modules/order/templates/commerce-order-receipt.html.twig", "/var/www/html/web/modules/contrib/commerce/modules/order/templates/commerce-order-receipt.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("block" => 48, "if" => 69, "for" => 120);
        static $filters = array("escape" => 31, "t" => 39, "commerce_price_format" => 116, "number_format" => 54);
        static $functions = array("url" => 31);

        try {
            $this->sandbox->checkSecurity(
                ['block', 'if', 'for'],
                ['escape', 't', 'commerce_price_format', 'number_format'],
                ['url']
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
