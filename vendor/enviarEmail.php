<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Carrega a biblioteca PHPMailer via Composer ou manualmente

function enviarEmailReserva($emailDestino, $nomeUsuario, $nomeLaboratorio, $data, $horaInicio, $horaFim, $descricao) {
    $mail = new PHPMailer(true);
    

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP (Gmail neste caso)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seuemail@gmail.com'; // Seu e-mail SMTP
        $mail->Password   = 'suasenha'; // Sua senha de aplicativo do Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('seuemail@gmail.com', 'Reserva de Laboratórios');
        $mail->addAddress($emailDestino, $nomeUsuario); // E-mail do usuário que fez a reserva

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de Reserva de Laboratório';
        $mail->Body    = "
            <h2>Confirmação de Reserva</h2>
            <p>Olá, $nomeUsuario, sua reserva foi confirmada com os seguintes detalhes:</p>
            <ul>
                <li><strong>Laboratório:</strong> $nomeLaboratorio</li>
                <li><strong>Data:</strong> $data</li>
                <li><strong>Horário:</strong> de $horaInicio até $horaFim</li>
                <li><strong>Descrição:</strong> $descricao</li>
            </ul>
            <p>Obrigado por usar nosso sistema de reservas!</p>
        ";

        // Enviar o e-mail
        $mail->send();
        echo 'E-mail enviado com sucesso.';
    } catch (Exception $e) {
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
}
