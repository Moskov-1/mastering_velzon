<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Su Contraseña de un Solo Uso</title>
</head>

<body style="margin: 0; padding: 20px; background-color: #f3f7f5; color: #14382e; font-family: Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f3f7f5;">
  <tr>
    <td align="center" style="padding: 20px 0;">
      <table width="520" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 520px; background-color: #ffffff; border-radius: 16px; border: 1px solid #dfeae5; box-shadow: 0 14px 30px rgba(20,56,46,0.10);">
        
        <!-- Encabezado -->
        <tr>
          <td style="text-align: center; padding: 22px 18px 20px; background-color: #6eaa89; color: #ffffff; position: relative;">
            <h1 style="margin: 0; font-size: 20px; font-weight: 800; margin-bottom: 6px; color: #ffffff;">Contraseña de un Solo Uso</h1>
            <p style="margin: 0; font-size: 13px; color: #ffffff; opacity: 0.95;">Su código seguro de verificación</p>
          </td>
        </tr>
        
        <!-- Separador -->
        <tr>
          <td style="height: 4px; background-color: #e8665d; padding: 0; margin: 0;"></td>
        </tr>

        <!-- Contenido -->
        <tr>
          <td style="padding: 22px 20px; background-color: #ffffff;">
            <p style="margin: 0 0 18px; font-size: 14px; line-height: 1.6; color: #14382e;">
              Utilice la siguiente contraseña de un solo uso (OTP) para completar su proceso de verificación.
              Este código es válido solo por un tiempo limitado.
            </p>

            <!-- Recuadro del OTP -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td style="background-color: #f7fbf9; border: 1px solid #dfeae5; border-radius: 14px; padding: 20px; text-align: center; margin: 18px 0;">
                  <h2 style="margin: 0 0 12px; font-size: 15px; font-weight: 800; color: #14382e;">Su código OTP</h2>

                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center">
                        <div style="display: inline-block; background-color: #e8665d; color: #ffffff; padding: 16px 28px; border-radius: 12px; font-size: 26px; font-weight: 900; letter-spacing: 0;">
                          {{ $otp }}
                        </div>
                      </td>
                    </tr>
                  </table>

                  <p style="margin: 14px 0 0; font-size: 13px; color: #5f7f73;">
                    Este OTP es válido durante <strong>{{ $ttl }} minutos</strong>.
                  </p>
                </td>
              </tr>
            </table>

            <p style="margin: 18px 0 0; font-size: 14px; line-height: 1.6; color: #14382e;">
              Por razones de seguridad, no comparta este código con nadie.
              Si usted no solicitó este código, por favor ignore este mensaje.
            </p>
          </td>
        </tr>

        <!-- Pie de página -->
        <tr>
          <td style="background-color: #f7fbf9; border-top: 1px solid #dfeae5; padding: 16px 18px; text-align: center; font-size: 12px; color: #5f7f73;">
            <p style="margin: 0; font-size: 12px; color: #5f7f73;">Este es un mensaje automático. Por favor, no responda a este correo electrónico.</p>
            <div style="margin: 6px 0 0; font-weight: 700; color: #6eaa89; font-size: 12px;">Protegido con cifrado de extremo a extremo</div>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>