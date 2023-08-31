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

/* modules/contrib/commerce/modules/checkout/templates/commerce-checkout-completion-message.html.twig */
class __TwigTemplate_0b527eef9b88ca488a91c650f725b933 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 14
        echo "<div class=\"checkout-complete\">";
        // line 15
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["message"] ?? null), 15, $this->source), "html", null, true);
        // line 17
        if (($context["payment_instructions"] ?? null)) {
            // line 18
            echo "    <div class=\"checkout-complete__payment-instructions\">
      <h2>";
            // line 19
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Payment instructions"));
            echo "</h2>
      ";
            // line 20
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["payment_instructions"] ?? null), 20, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 23
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "modules/contrib/commerce/modules/checkout/templates/commerce-checkout-completion-message.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  58 => 23,  52 => 20,  48 => 19,  45 => 18,  43 => 17,  41 => 15,  39 => 14,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/commerce/modules/checkout/templates/commerce-checkout-completion-message.html.twig", "/var/www/html/web/modules/contrib/commerce/modules/checkout/templates/commerce-checkout-completion-message.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 17);
        static $filters = array("escape" => 15, "t" => 19);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 't'],
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
