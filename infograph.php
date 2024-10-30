<?php
/*
Plugin Name: Infograph - Display your blog info with graphs
Plugin URI: http://www.webania.net/
Description:  Display your blog info with graphs.
Version: 1.0
Author: Elvin Haci
Author URI: http://webania.net
License: GPL2
*/
/*  Copyright 2011,  Elvin Haci  (email : elvinhaci@hotmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



function myscripts ()

{
wp_deregister_script('jquery'); 
wp_register_script( 'jquery',get_bloginfo('wpurl') . "/wp-content/plugins/infograph/jquery-1.3.2.min.js");
wp_enqueue_script('jquery'); 
 
wp_register_script( 'jqplot',get_bloginfo('wpurl') . "/wp-content/plugins/infograph/src/jquery.jqplot.js");
wp_enqueue_script( 'jqplot' );

wp_register_script( 'bar', get_bloginfo('wpurl') . "/wp-content/plugins/infograph/src/plugins/jqplot.barRenderer.js");
wp_enqueue_script( 'bar' );

wp_register_script( 'cax', get_bloginfo('wpurl') . "/wp-content/plugins/infograph/src/plugins/jqplot.categoryAxisRenderer.js");
wp_enqueue_script( 'cax' );

wp_register_script( 'pol', get_bloginfo('wpurl') . "/wp-content/plugins/infograph/src/plugins/jqplot.pointLabels.js");
wp_enqueue_script( 'pol' );
 
wp_register_script( 'fun', get_bloginfo('wpurl') . "/wp-content/plugins/infograph/src/plugins/jqplot.funnelRenderer.js");
wp_enqueue_script( 'fun' );

wp_register_script( 'pie', get_bloginfo('wpurl') . "/wp-content/plugins/infograph/src/plugins/jqplot.pieRenderer.js");
wp_enqueue_script( 'pie' );

wp_register_script( 'meg', get_bloginfo('wpurl') . "/wp-content/plugins/infograph/src/plugins/jqplot.meterGaugeRenderer.js");
wp_enqueue_script( 'meg' );

}



function add_css () {
	 echo '
	 <link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/infograph/src/jquery.jqplot.css" />' ;
	 echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/infograph/examples.css" />' ;
} 

add_action('wp_enqueue_scripts', 'myscripts');
add_action('wp_head', 'add_css'); 


function categories($atts,$content = '') {
$ch_cats = get_categories(array('orderby'=>'count','order'=>'desc') );
$sayy= count($ch_cats);
$chl='';
for ($i=1;$i<=5;$i++)
  {
    $chl=$chl.'[\''.$ch_cats[$i-1]->name.'\','.$ch_cats[$i-1]->count.'],';  //[[[\'a\',25],[\'b\',14],[\'c\',7]]]
     }

$chl='[['.substr($chl,0,-1).']]';  

return '<script>
$(document).ready(function(){
plot1 = $.jqplot(\'chart1\', '.$chl.', {
title: \'Most popular categories of example.com\',
seriesDefaults:{renderer:$.jqplot.PieRenderer,rendererOptions: { padding: 8, showDataLabels: true}},legend:{show:true,placement: \'outside\',rendererOptions: {numberRows: 1}},
legend:{show:true, 
        placement: \'outside\', 
        rendererOptions: {
        numberRows: 1
                     }, 
        location:\'s\',
        marginTop: \'15px\'
                  }       
  });
});
  </script>
  <div id="chart1" style="margin-top:30px;margin-bottom:30px; margin-left:20px; width:500px; height:300px;"></div>  ';
 }


function postsbymonth($atts,$content = '') {
	// Post count by month
global $wpdb,$wp_query;
$postcountbymonth=$wpdb->get_results("select * from (select  MONTH(post_date) as mo,YEAR(post_date) as ye,count(ID) as co from $wpdb->posts 
where post_status='publish'
group by MONTH(post_date),YEAR(post_date) order by post_date desc limit 12) a order by ye asc,mo asc");
$labels='';$postcounts='';
foreach ($postcountbymonth as $pc) 
 {
	 $labels=$labels.'\' '.$pc->mo.'/'.$pc->ye.' \',';
	 $postcounts=$postcounts.$pc->co.',';
 }
$postcounts='['.substr($postcounts,0,-1).']';
$labels='['.substr($labels,0,-1).']';
  
return '
<script language="javascript" type="text/javascript">
$(document).ready(function(){
        $.jqplot.config.enablePlugins = true;
        var s1 = '.$postcounts.';
        var ticks = '.$labels.';
        plot1 = $.jqplot(\'chart2\', [s1], {
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: false }
        });
    });
</script>
  <div id="chart2" style="margin-top:20px;margin-bottom:20px; margin-left:20px; width:500px; height:250px;"></div>
  ';
 }

 

function mostcommented($atts,$content = '') {
global $wpdb,$wp_query;
$postcountbymonth=$wpdb->get_results("select post_title as pt,comment_count as co from $wpdb->posts where post_status='publish' order by co desc limit 5");
$labels='';$titles='';
foreach ($postcountbymonth as $pc) 
 {
	 $titles=$titles.'[\' '.$pc->pt.' ('.$pc->pt.') \','.$pc->co.'],';
	 
 }

$titles='['.substr($titles,0,-1).']';
  return '
  <style>.jqplot-table-legend{width:200px}
  #chart4 table tr td:nth-child(odd){width:20px;}
  </style>
<script  type="text/javascript">
$(document).ready(function(){
    s1 = '.$titles.';
    plot4 = $.jqplot(\'chart4\', [s1], {
        seriesDefaults:{
            renderer:$.jqplot.FunnelRenderer,
            rendererOptions: {
                showDataLabels: false
               
            }
        },
        legend: {
            show: true,
            placement: \'outside\'
        }
    });
});
</script>
  <div id="chart4" style="margin-top:20px;margin-bottom:20px; margin-left:20px; width:350px; height:300px;"></div>
  ';
 }

function velocity($atts,$content = '') {

global $wpdb,$wp_query;

$postcountbymonth=$wpdb->get_results("select post_title as pt,comment_count as co from $wpdb->posts where post_status='publish'");
$postcountresult= round($wpdb->num_rows/12);

$maxvel=pow(10,strlen((string)$postcountresult));
$ticks='';	
$intervals='';	

for ($i=1;$i<=6;$i++){
	$ticks=$ticks.($maxvel*($i-1)/5).',';
	if($i % 2 != 0)
	  {
		  $intervals=$intervals.($maxvel*($i-1)/5).',';
		  }
	}

$ticks='['.substr($ticks,0,-1).']';	
$intervals='['.substr($intervals,0,-1).']';	

  return '
  <script>
  $(document).ready(function(){
   s1 = ['.$postcountresult.'];
   plot5 = $.jqplot(\'chart5\',[s1],{
       title: \'Your blog post velocity by month\',
       series: [{
           renderer: $.jqplot.MeterGaugeRenderer,
           rendererOptions: {
               label: \'Blog velocity\',
               labelPosition: \'bottom\',
               ticks: '.$ticks.',
               intervals:'.$intervals.',
           }
       }],
   });
});
</script>
<div id="chart5" style="margin-top:20px;margin-bottom:20px; margin-left:20px; width:350px; height:300px;"></div>';
 }

 
add_shortcode('velocity', 'velocity'); 
add_shortcode('mycategories', 'categories');
add_shortcode('bymonth', 'postsbymonth');
add_shortcode('mypopularposts', 'mostcommented');





?>
