<?php
header('Content-Type: application/json; charset=utf-8');
require_once "config/database.php";

$q = trim($_POST['question'] ?? '');
if ($q === '') { echo json_encode(['ok'=>false]); exit; }

$normalized = strtolower(preg_replace('/[^a-z0-9\s]/i',' ', $q));
$words = array_filter(preg_split('/\s+/', $normalized), fn($w)=>strlen($w)>=3);

$res = $conn->query("SELECT * FROM services ORDER BY id");
$best = null; $scoreBest = 0;

while($row=$res->fetch_assoc()){
    $text=strtolower($row['service_name'].' '.$row['description'].' '.$row['service_procedure']);
    $score=0;
    foreach($words as $w) if(strpos($text,$w)!==false) $score++;
    $name=strtolower($row['service_name']);
    if(strpos($normalized,'passport')!==false && strpos($name,'passport')!==false) $score+=6;
    if(strpos($normalized,'license')!==false && strpos($name,'license')!==false) $score+=6;
    if((strpos($normalized,'business')!==false||strpos($normalized,'registration')!==false) && strpos($name,'business')!==false) $score+=5;
    if($score>$scoreBest){$scoreBest=$score;$best=$row;}
}

if(!$best){
 echo json_encode(['ok'=>true,'answer'=>'<p>I could not find a matching service in the current MISA knowledge base.</p><p>Try <strong>passport renewal</strong>, <strong>driver’s license renewal</strong>, or <strong>business registration</strong>.</p>']);
 exit;
}

$id=(int)$best['id'];
$stmt=$conn->prepare("SELECT d.name FROM documents d JOIN service_documents sd ON d.id=sd.document_id WHERE sd.service_id=? ORDER BY d.name");
$stmt->bind_param("i",$id); $stmt->execute(); $docs=$stmt->get_result();
$list='';
while($d=$docs->fetch_assoc()) $list.='<li>'.htmlspecialchars($d['name']).'</li>';

$html='<div class="answer-title">'.htmlspecialchars($best['service_name']).'</div>';
$html.='<p>'.htmlspecialchars($best['description']).'</p>';
$html.='<div class="facts">';
$html.='<div><small>Procedure</small><b>'.nl2br(htmlspecialchars($best['service_procedure'])).'</b></div>';
$html.='<div><small>Fee</small><b>'.htmlspecialchars($best['fee']).'</b></div>';
$html.='<div><small>Processing time</small><b>'.htmlspecialchars($best['processing_time']).'</b></div>';
$html.='</div>';
if($list) $html.='<div class="docs"><b>Required documents</b><ul>'.$list.'</ul></div>';
$html.='<div class="source"><b>Source</b> '.htmlspecialchars($best['source']).'</div>';
$html.='<a class="details" href="service-details.php?id='.$id.'">View full service details →</a>';

echo json_encode(['ok'=>true,'answer'=>$html]);
?>