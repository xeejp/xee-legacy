<?php
class ScatterGraph extends ModUIComponent{
    private $data;
    public function __construct($data){
        $this->data = $data;
    }
    public function get_templates($name){
        $template = '<div class="pure-img" id="'. $this->get_template_name($name) .'-graph" style="border: solid 1px;"></div>';
        return [$this->get_template_name($name) => $template];
    }
    public function get_scripts($name){
        $id = $this->get_template_name($name) .'-graph';
        $json = json_encode($this->data);
        return [
            'other' => <<<JS
(function(\$){

var data = \$.parseJSON('{$json}');
var min = {x:  0, y:  0};
var max = {x: 10, y: 10};

for (var key in data) {
    var d = data[key];
    if (d.x < min.x) min.x = d.x;
    if (d.y < min.y) min.y = d.y;
    if (d.x > max.x) max.x = d.x;
    if (d.y > max.y) max.y = d.y;
}

var width = 300, height = 300;
var padding = {
    left  : 50,
    right : 20,
    top   : 20,
    bottom: 30,
};
\$('#{$id}').width(width + padding.left + padding.right).height(height + padding.top + padding.bottom);


var scale = {
    x: d3.scale.linear()
        .domain([min.x, max.x])
        .range([0, width]),
    y: d3.scale.linear()
        .domain([min.y, max.y])
        .range([0, height]),
};

var axis = {
    x: d3.svg.axis()
        .scale(scale.x)
        .orient("bottom"),
    y: d3.svg.axis()
        .scale(scale.y)
        .orient("top"),
};

var svg = d3.select('#{$id}')
    .append('svg')
    .attr('width', width + padding.left + padding.right)
    .attr('height', height + padding.top + padding.bottom);

svg.selectAll('circle')
    .data(data)
    .enter()
    .append('circle')
        .attr('cx', function(d){ return scale.x(d.x); })
        .attr('cy', function(d){ return scale.y(max.y - d.y); })
        .attr('r', 4)
        .attr('fill', 'red')
        .attr('transform', 'translate('+ padding.left +','+ padding.top +')');

svg.append('g')
    .attr('class', 'axis')
    .call(axis.x)
    .attr('transform', 'translate('+ padding.left +','+ (padding.top + height) +')');
svg.append('g')
    .attr('class', 'axis')
    .call(axis.y)
    .attr('transform', 'translate('+ padding.left +','+ (padding.top + height) +') rotate(-90)');
})(jQuery);

JS
        ];
    }
}
