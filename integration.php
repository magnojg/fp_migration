<?php

$INDEX_STUDENT_CODE = 0;
$INDEX_STUDENT_NAME = 1;
$INDEX_STUDENT_CPF = 2;
$INDEX_STUDENT_EMAIL = 3;
$INDEX_COURSE_ID = 4;
$INDEX_COURSE_TITLE = 5;
$INDEX_ENROLLMENT_START_AT = 6;
$INDEX_ENROLLMENT_END_AT = 7;
$INDEX_CLASS_TITLE = 8;
$INDEX_STUDENT_DESIRED_AREA = 9;
$INDEX_STUDENT_DESIRED_COURSE = 10;
$INDEX_ENROLLMENT_SITUATION = 11;
$INDEX_HAS_PHOTO = 12;
$INDEX_LOCAL = 13;

$seed_name = '';

$csvs_to_import = glob("../integracao/*.csv");

foreach($csvs_to_import as $csv) {
  $timestamp = explode("-", $csv)[1];
  $timestamp = substr($timestamp, 0, strrpos($timestamp, "."));
  $seed_name = "seeds-" . $timestamp  . ".rb";

  // $filename = 'alunos_matriculas.csv';

  // if(!file_exists($filename)) {
  //   die("Arquivo $filename inexistente");
  // }

  $ar_courses = array();

  $handle = fopen($csv, 'r+');

  write("# -*- encoding : utf-8 -*-" . PHP_EOL);
  write("ActiveRecord::Base.transaction do");
  write("account = Account.find_or_create_by(name: 'Fernanda Pessoa')" . PHP_EOL);
  write("user = User.find_or_create_by(email: 'magno.silveira@morustecnologia.com.br') do |u|" . PHP_EOL
          . "\tu.first_name = 'Magno'" . PHP_EOL
          . "\tu.last_name = 'Silveira'" . PHP_EOL
          . "\tu.password = '123123'" . PHP_EOL
          . "\tu.password_confirmation = '123123'" . PHP_EOL
          . "\tu.account_id = account.id" . PHP_EOL
        . "end;" . PHP_EOL);

  write("user = User.find_or_create_by(email: 'robinson@fernandapessoa.com.br') do |u|" . PHP_EOL
          . "\tu.first_name = 'Robinson'" . PHP_EOL
          . "\tu.last_name = 'Salvador'" . PHP_EOL
          . "\tu.password = '123123'" . PHP_EOL
          . "\tu.password_confirmation = '123123'" . PHP_EOL
          . "\tu.account_id = account.id" . PHP_EOL
        . "end;" . PHP_EOL);

  write("user = User.find_or_create_by(email: 'otavio@fernandapessoa.com.br') do |u|" . PHP_EOL
          . "\tu.first_name = 'Luiz'" . PHP_EOL
          . "\tu.last_name = 'Otávio'" . PHP_EOL
          . "\tu.password = '123123'" . PHP_EOL
          . "\tu.password_confirmation = '123123'" . PHP_EOL
          . "\tu.account_id = account.id" . PHP_EOL
        . "end;" . PHP_EOL);

  write("user = User.find_or_create_by(email: 'felipegabardo@gmail.com') do |u|" . PHP_EOL
      . "\tu.first_name = 'Felipe'" . PHP_EOL
      . "\tu.last_name = 'Gabardo'" . PHP_EOL
      . "\tu.password = '123123'" . PHP_EOL
      . "\tu.password_confirmation = '123123'" . PHP_EOL
      . "\tu.account_id = account.id" . PHP_EOL
      . "end;" . PHP_EOL);

  write("domain = Domain.find_or_create_by({ name: 'fernandapessoa.com.br', account_id: account.id })" . PHP_EOL);

  write("teacher_fp = Teacher.find_or_create_by(email: 'fernanda@fernandapessoa.com.br') do |t|" . PHP_EOL
          . "\tt.first_name = 'Fernanda'" . PHP_EOL
          . "\tt.last_name = 'Pessoa'" . PHP_EOL
          . "\tt.password = '123123'" . PHP_EOL
          . "\tt.password_confirmation = '123123'" . PHP_EOL
          . "\tt.account_id = account.id" . PHP_EOL
        . "end;" . PHP_EOL);

  write("magno = Student.where(\"email = 'magnojg@gmail.com'\").first " . PHP_EOL
          . "if magno.nil?" . PHP_EOL
          . "\tmagno = Student.new(" . PHP_EOL
          . "\tfirst_name: 'Magno', " . PHP_EOL
          . "\tlast_name: 'Silveira', " . PHP_EOL
          . "\temail: 'magnojg@gmail.com', " . PHP_EOL
          . "\tcpf: '06656697470', " . PHP_EOL
          . "\tpassword: '06656697470', " . PHP_EOL
          . "\tpassword_confirmation: '06656697470', " . PHP_EOL
          . "\taccount_id: account.id, " . PHP_EOL
          . "\tmigrated_id: 189999)" . PHP_EOL
          . "else " . PHP_EOL
          . "\tmagno.assign_attributes(first_name: 'Magno', last_name: 'Silveira', email: 'magnojg@gmail.com', cpf: '06656697470', migrated_id: 189999)" . PHP_EOL
          . "end;" . PHP_EOL
          . "magno.save(validate: false)" . PHP_EOL);

  while (($data = fgetcsv($handle)) !== FALSE) {
    $row = explode(';', $data[0]);

    if(empty($row[$INDEX_STUDENT_EMAIL])) {
      continue;
    }

    $student_code = trim(preg_replace('~\D~', '', $row[$INDEX_STUDENT_CODE]));
    $course_id = $row[$INDEX_COURSE_ID];
    $student_name = ($row[$INDEX_STUDENT_NAME]);
    $course_title = ($row[$INDEX_COURSE_TITLE]);
    $student_email = strtolower($row[$INDEX_STUDENT_EMAIL]);
    $student_cpf = trim(preg_replace('~\D~', '', $row[$INDEX_STUDENT_CPF]));

    $student_var = "student_" . $student_cpf;
    $course_var = "course_" . $course_id;
    $enrollment_var = "enrollment_" . $student_var . "_course_" . $course_id;

    if(!in_array($course_id, $ar_courses)) {
      write("$course_var = Course.find_or_create_by(migrated_id: " . $course_id . ") do |c| " . PHP_EOL
            . "\tc.name = '" . $course_title . "'" . PHP_EOL
            . "\tc.thumb_file_name = 'thumb.jpg'" . PHP_EOL
            . "\tc.thumb_content_type = 'image/jpeg'" . PHP_EOL
            . "\tc.thumb_file_size = 50446" . PHP_EOL
            . "\tc.thumb_updated_at = '2017-04-30 23:57:26.949598'" . PHP_EOL
            . "\tc.account_id = account.id" . PHP_EOL
          . "end;" . PHP_EOL);

      $ar_courses[] = $course_id;
    }

    write("$student_var = Student.where(\"migrated_id = $student_code OR email = '$student_email'\").first " . PHP_EOL
          . "if $student_var.nil?" . PHP_EOL
          . "\t$student_var = Student.new(" . PHP_EOL
          . "\tfirst_name: '" . $student_name . "', " . PHP_EOL
          . "\temail: '" . $student_email . "', " . PHP_EOL
          . "\tcpf: '" . $student_cpf . "', " . PHP_EOL
          . "\tpassword: '" . $student_cpf . "', " . PHP_EOL
          . "\tpassword_confirmation: '" . $student_cpf . "', " . PHP_EOL
          . "\taccount_id: account.id, " . PHP_EOL
          . "\tmigrated_id: " . $student_code . ")" . PHP_EOL
          . "else " . PHP_EOL
          . "\t$student_var.assign_attributes(first_name: '" . $student_name . "', email: '" . $student_email . "', cpf: '" . $student_cpf . "', migrated_id: " . $student_code . ")" . PHP_EOL
          . "end;" . PHP_EOL
          . "$student_var.save(validate: false)" . PHP_EOL);

    write("$enrollment_var = Enrollment.where(student_id: $student_var.id, course_id: $course_var.id).first " . PHP_EOL
          . "if $enrollment_var.nil?" . PHP_EOL
          . "\t$enrollment_var = Enrollment.new(" . PHP_EOL
          . "\tstudent_id: $student_var.id, " . PHP_EOL
          . "\tcourse_id: $course_var.id, " . PHP_EOL
          . "\tdomain_id: domain.id, " . PHP_EOL
          . "\taccount_id: account.id, " . PHP_EOL
          . "\tstatus: " . getEnrollmentStatus($row) . "," . PHP_EOL
          . "\tcreated_at: '" . $row[$INDEX_ENROLLMENT_START_AT] . "')" . PHP_EOL
          . "else " . PHP_EOL
          . "\t$enrollment_var.assign_attributes(student_id: $student_var.id, course_id: $course_var.id, domain_id: domain.id, account_id: account.id, status: " . getEnrollmentStatus($row) . ", created_at: '" . $row[$INDEX_ENROLLMENT_START_AT] . "')" . PHP_EOL
        . "end;" . PHP_EOL
        . "$enrollment_var.save(validate: false)" . PHP_EOL);
  }

  write('end');

  echo 'arquivo gerado com sucesso!<br>' . PHP_EOL;

  $file_path = explode('/', $csv);
  if(rename($csv, '../cron_integration/migrated/' . end($file_path))) {
    echo "arquivo movido com sucesso! $csv<br>";
  } else {
    echo "arquivo não movido: $csv<br>";
  }
}

commitSeeds();

function commitSeeds() {
  $seeds = glob("seed*.rb");

  if(!empty($seeds)) {
    require_once('Git.php');

    $repo = Git::open('.');  // -or- Git::create('/path/to/repo')

    if(!Git::is_repo($repo)) {
      $repo = Git::create('.');
      echo "Git::create<br>" . PHP_EOL;
    } else {
      echo "Git::open<br>" . PHP_EOL;
    }

    $msg = "";
    foreach ($seeds as $seed) {
      $repo->add($seed);

      $msg .= "Added $seed" . PHP_EOL;
    }

    if(!empty($msg)) {
      echo "msg: $msg<br>";
      $repo->commit($msg);
      $repo->push('origin', 'master');
      echo "commited<br>" . PHP_EOL;
    } else {
      echo "msg null<br>";
    }
  }
}

function write($str) {
  global $seed_name;

  file_put_contents($seed_name, str_replace("\t", "  ", $str) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function getEnrollmentStatus($row) {
  global $INDEX_ENROLLMENT_START_AT;
  global $INDEX_ENROLLMENT_END_AT;
  global $INDEX_ENROLLMENT_SITUATION;

  $situation = @$row[$INDEX_ENROLLMENT_SITUATION];
  if($situation == 'A') {
    $start_at = strtotime($row[$INDEX_ENROLLMENT_START_AT]);

    $end_at = (!empty($row[$INDEX_ENROLLMENT_END_AT]))? strtotime($row[$INDEX_ENROLLMENT_END_AT]) : null;

    $curtime = time();
    if($start_at <= $curtime) {

      if(!empty($end_at) && $end_at < $curtime) {
        return "'disabled'";
      }

      return "'enabled'";
    }

    return "'disabled'";

  }
  return "'disabled'";
}
