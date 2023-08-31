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

/* modules/contrib/commerce/modules/checkout/templates/commerce-checkout-order-summary.html.twig */
class __TwigTemplate_58308855c31933b927a1a2f717fac648 extends \Twig\Template
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
            'totals' => [$this, 'block_totals'],
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 23
        echo "<div";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => "checkout-order-summary"], "method", false, false, true, 23), 23, $this->source), "html", null, true);
        echo ">
  ";
        // line 24
        $this->displayBlock('order_items', $context, $blocks);
        // line 41
        echo "  ";
        $this->displayBlock('totals', $context, $blocks);
        // line 44
        echo "</div>";
    }

    // line 24
    public function block_order_items($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 25
        echo "    <table>
      <tbody>
      ";
        // line 27
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["order_entity"] ?? null), "getItems", [], "any", false, false, true, 27));
        foreach ($context['_seq'] as $context["_key"] => $context["order_item"]) {
            // line 28
            echo "        <tr>
          <td>";
            // line 29
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, twig_number_format_filter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["order_item"], "getQuantity", [], "any", false, false, true, 29), 29, $this->source)), "html", null, true);
            echo "&nbsp;x</td>
          ";
            // line 30
            if (twig_get_attribute($this->env, $this->source, $context["order_item"], "hasPurchasedEntity", [], "any", false, false, true, 30)) {
                // line 31
                echo "            <td>";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\commerce\TwigExtension\CommerceTwigExtension']->renderEntity($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["order_item"], "getPurchasedEntity", [], "any", false, false, true, 31), 31, $this->source), "summary"), "html", null, true);
                echo "</td>
          ";
            } else {
                // line 33
                echo "            <td>";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["order_item"], "label", [], "any", false, false, true, 33), 33, $this->source), "html", null, true);
                echo "</td>
          ";
            }
            // line 35
            echo "          <td>";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\commerce_price\TwigExtension\PriceTwigExtension']->formatPrice($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["order_item"], "getTotalPrice", [], "any", false, false, true, 35), 35, $this->source)), "html", null, true);
            echo "</td>
        </tr>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['order_item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 38
        echo "      </tbody>
    </table>
  ";
    }

    // line 41
    public function block_totals($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 42
        echo "    ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["rendered_totals"] ?? null), 42, $this->source), "html", null, true);
        echo "
  ";
    }

    public function getTemplateName()
    {
        return "modules/contrib/commerce/modules/checkout/templates/commerce-checkout-order-summary.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  108 => 42,  104 => 41,  98 => 38,  88 => 35,  82 => 33,  76 => 31,  74 => 30,  70 => 29,  67 => 28,  63 => 27,  59 => 25,  55 => 24,  51 => 44,  48 => 41,  46 => 24,  41 => 23,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/commerce/modules/checkout/templates/commerce-checkout-order-summary.html.twig", "/var/www/html/web/modules/contrib/commerce/modules/checkout/templates/commerce-checkout-order-summary.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("block" => 24, "for" => 27, "if" => 30);
        static $filters = array("escape" => 23, "number_format" => 29, "commerce_entity_render" => 31, "commerce_price_format" => 35);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['block', 'for', 'if'],
                ['escape', 'number_format', 'commerce_entity_render', 'commerce_price_format'],
                []
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
