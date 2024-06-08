<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* filters.html.twig */
class __TwigTemplate_e2bffa45274d0ee1f319e2d1d38c9028 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "filters.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "filters.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 4
        yield "    <div class=\"container mt-4\">
        <h1 class=\"mb-4\">Filtrer les événements</h1>
        <form method=\"get\" action=\"";
        // line 6
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("event_filter");
        yield "\" class=\"mb-4\">
            <div class=\"form-group\">
                <label for=\"name\">Nom (3 lettres minimum)</label>
                <input type=\"text\" id=\"name\" name=\"name\" class=\"form-control\" minlength=\"3\" placeholder=\"Entrez le nom\">
            </div>
            <div class=\"form-group\">
                <label for=\"date\">Date</label>
                <input type=\"date\" id=\"date\" name=\"date\" class=\"form-control\">
            </div>
            <div class=\"form-group\">
                <label for=\"isPublic\">Évènements disponibles</label>
                <select id=\"isPublic\" name=\"isPublic\" class=\"form-control\">
                    <option value=\"\">Tous</option>
                    <option value=\"1\">Oui</option>
                    <option value=\"0\">Non</option>
                </select>
            </div>
            <button type=\"submit\" class=\"btn btn-primary mt-2\">Filtrer</button>
        </form>
    </div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "filters.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  64 => 6,  60 => 4,  53 => 3,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html.twig' %}

{% block body %}
    <div class=\"container mt-4\">
        <h1 class=\"mb-4\">Filtrer les événements</h1>
        <form method=\"get\" action=\"{{ path('event_filter') }}\" class=\"mb-4\">
            <div class=\"form-group\">
                <label for=\"name\">Nom (3 lettres minimum)</label>
                <input type=\"text\" id=\"name\" name=\"name\" class=\"form-control\" minlength=\"3\" placeholder=\"Entrez le nom\">
            </div>
            <div class=\"form-group\">
                <label for=\"date\">Date</label>
                <input type=\"date\" id=\"date\" name=\"date\" class=\"form-control\">
            </div>
            <div class=\"form-group\">
                <label for=\"isPublic\">Évènements disponibles</label>
                <select id=\"isPublic\" name=\"isPublic\" class=\"form-control\">
                    <option value=\"\">Tous</option>
                    <option value=\"1\">Oui</option>
                    <option value=\"0\">Non</option>
                </select>
            </div>
            <button type=\"submit\" class=\"btn btn-primary mt-2\">Filtrer</button>
        </form>
    </div>
{% endblock %}
", "filters.html.twig", "/var/www/project_php/templates/filters.html.twig");
    }
}
