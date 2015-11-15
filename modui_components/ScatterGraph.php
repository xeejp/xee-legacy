<?php
class ScatterGraph extends ModUIComponent{
    const UPDATE_INTERVAL = 5000;
    private $data, $option;
    public function __construct($data, $option=[]){
        foreach ($data as $key => $val) {
            if (!isset($data[$key]['color']))
                $data[$key]['color'] = 'black';
        }
        if (!isset($option['label']['x'])) $option['label']['x'] = 'x';
        if (!isset($option['label']['y'])) $option['label']['y'] = 'y';
        $this->data = $data;
        $this->option = $option;
    }
    public function get_templates($name){
        $template = '<div class="pure-img" id="'. $this->get_template_name($name) .'-graph" style="border: solid 1px;" data-plot="{data}"></div>';
        return [$this->get_template_name($name) => $template];
    }
    public function get_values ($name) {
        return ['data' => base64_encode(json_encode($this->data))];
    }
    public function get_scripts($name){
        $id = $this->get_template_name($name) .'-graph';
        $interval = self::UPDATE_INTERVAL;
        return [
            'other' => <<<JS
var base64 = \$('#{$id}').attr('data-plot');
draw_graph();
setInterval(function () {
    var new_base64 = \$('#{$id}').attr('data-plot');
    if (base64 == new_base64) return;
    base64 = new_base64;
    \$('#{$id}').empty();
    draw_graph();
}, {$interval});

function draw_graph () {
// size
var width = 480, height = 480;
var padding = {
    left  : 70,
    right : 20,
    top   : 20,
    bottom: 50,
};

// data
var data;
try {
    data = \$.parseJSON(atob(base64));
} catch (e) {
    data = {};
}

var min = {x:  0, y:  0};
var max = {x: 10, y: 10};
\$.each(data, function(key) {
    \$.each(data[key]['values'], function(i, d) {
        if (d.x < min.x) min.x = d.x;
        if (d.y < min.y) min.y = d.y;
        if (d.x > max.x) max.x = d.x;
        if (d.y > max.y) max.y = d.y;
    }); 
});
var scale = {
    x: d3.scale.linear()
        .domain([min.x, max.x])
        .range([0, width]),
    y: d3.scale.linear()
        .domain([min.y, max.y])
        .range([height, 0]),
};
var axis = {
    x: d3.svg.axis()
        .scale(scale.x)
        .ticks(10)
        .orient("bottom"),
    y: d3.svg.axis()
        .scale(scale.y)
        .ticks(5)
        .orient("left"),
};

// draw
\$('#{$id}')
    .width(width + padding.left + padding.right)
    .height(height + padding.top + padding.bottom);
var svg = d3.select('#{$id}')
    .append('svg')
    .attr('width', width + padding.left + padding.right)
    .attr('height', height + padding.top + padding.bottom);
svg.append('g')
    .attr('class', 'axis')
    .call(axis.x)
    .attr('transform', 'translate('+ padding.left +','+ (padding.top + height) +')');
svg.append('g')
    .attr('class', 'axis')
    .call(axis.y)
    .attr('transform', 'translate('+ padding.left +','+ padding.top +')');
svg.append("text")
    .text('{$this->option['label']['y']}')
    .attr('dy', '1em')
    .attr('transform', 'translate(5,'+ padding.top/2 +') rotate(-90)')
    .style("text-anchor", "end");
svg.append("text")
    .text('{$this->option['label']['x']}')
    .attr('dy', '1em')
    .attr('transform', 'translate('+ (padding.left + width + padding.right/2) +','+ (padding.top + height + padding.bottom/2) +')')
    .style("text-anchor", "end");

// plot
\$.each(data, function (key) {
    svg.selectAll('circle'+ key)
        .data(data[key]['values'])
        .enter()
        .append('circle')
            .attr('cx', function(d){ return scale.x(d.x); })
            .attr('cy', function(d){ return scale.y(d.y); })
            .attr('r', 4)
            .attr('fill', data[key]['color'])
            .attr('transform', 'translate('+ padding.left +','+ padding.top +')');
});
}

JS
        ];
    }
}
