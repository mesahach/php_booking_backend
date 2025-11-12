<?php
namespace MyApp\Utils;

use MyApp\Entity\UserModelEntity;

final class EmailMessagesClass
{
  private $notificationMessage;

  public static function setNotificationMessage($title, $messageInfo): string
  {
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <title>' . htmlspecialchars($title) . '</title>
    </head>
    <body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#333;">
      <center style="width:100%;background:#f4f4f7;">
        <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05)">
          
          <!-- HEADER -->
          <div style="background:#30e3ca;padding:20px;text-align:center;">
            <img src="' . siteLink . '/images/logo.png" alt="' . siteName . ' Logo" style="height:60px;">
          </div>

          <!-- TITLE -->
          <div style="padding:30px 20px;text-align:center;border-bottom:1px solid #eee;">
            <h1 style="margin:0;font-size:22px;color:#222;">' . htmlspecialchars($title) . '</h1>
          </div>

          <!-- BODY CONTENT -->
          <div style="padding:20px;line-height:1.6;font-size:16px;color:#444;">
            ' . $messageInfo . '
          </div>

          <!-- CTA BUTTON -->
          <div style="padding:20px;text-align:center;">
            <a href="' . siteLink . '" 
              style="background:#30e3ca;color:#fff;text-decoration:none;padding:12px 24px;
              border-radius:5px;font-size:16px;display:inline-block;">
              Visit ' . siteName . '
            </a>
          </div>

          <!-- FOOTER -->
          <div style="background:#fafafa;padding:20px;font-size:14px;color:#777;text-align:center;border-top:1px solid #eee;">
            <p style="margin:0 0 8px;">Contact us anytime:</p>
            <p style="margin:0;">
              <a href="mailto:support@' . siteDomain . '" style="color:#30e3ca;">support@' . siteDomain . '</a> | 
              <a href="mailto:info@' . siteDomain . '" style="color:#30e3ca;">info@' . siteDomain . '</a>
            </p>
            <p style="margin-top:15px;font-size:12px;color:#aaa;">
              © ' . date("Y") . ' ' . siteName . '. All rights reserved.
            </p>
          </div>
        </div>
      </center>
    </body>
    </html>
    ';

    return $message;
  }

  public static function setOtpEmail(string $title, string $otpCode, int $expiryMinutes): string
  {
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <title>' . htmlspecialchars($title) . '</title>
    </head>
    <body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#333;">
      <center style="width:100%;background:#f4f4f7;padding:20px 0;">
        <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:8px;
          overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05);">

          <!-- HEADER -->
          <div style="background:#30e3ca;padding:15px;text-align:center;">
            <h2 style="margin:0;font-size:20px;color:#fff;font-weight:600;">' . htmlspecialchars($title) . '</h2>
          </div>

          <!-- BODY -->
          <div style="padding:30px;text-align:center;line-height:1.6;">
            <p style="margin:0 0 15px;font-size:16px;color:#444;">
              Use the One-Time Password (OTP) below to continue:
            </p>

            <!-- OTP CODE -->
            <div style="font-size:32px;font-weight:bold;letter-spacing:5px;color:#222;
              background:#f9f9f9;border:1px dashed #ccc;display:inline-block;
              padding:12px 24px;margin:20px 0;border-radius:6px;">
              ' . htmlspecialchars($otpCode) . '
            </div>

            <p style="margin:0;font-size:14px;color:#666;">
              This OTP will expire in <strong>' . intval($expiryMinutes) . ' minutes</strong>.
            </p>
          </div>

          <!-- FOOTER -->
          <div style="background:#fafafa;padding:15px;text-align:center;font-size:13px;color:#999;">
            <p style="margin:0;">If you didn’t request this code, you can safely ignore this email.</p>
            <p style="margin:10px 0 0;">&copy; ' . date("Y") . ' ' . siteName . '</p>
          </div>

        </div>
      </center>
    </body>
    </html>';

    return $message;
  }

  public static function setWelcomeMessage($title, $messageInfo, $token, UserModelEntity $user_data): string
  {
    $message = '
<!DOCTYPE html>
<html
  lang="en"
  xmlns:o="urn:schemas-microsoft-com:office:office"
  xmlns:v="urn:schemas-microsoft-com:vml"
>
  <head>
    <title>' . $title . '</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <style>
      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        padding: 0;
      }

      a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: inherit !important;
      }

      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
      }

      p {
        line-height: inherit;
      }

      .desktop_hide,
      .desktop_hide table {
        mso-hide: all;
        display: none;
        max-height: 0px;
        overflow: hidden;
      }

      .image_block img + div {
        display: none;
      }

      sup,
      sub {
        line-height: 0;
        font-size: 75%;
      }

      .menu_block.desktop_hide .menu-links span {
        mso-hide: all;
      }

      @media (max-width: 700px) {
        .desktop_hide table.icons-outer {
          display: inline-table !important;
        }

        .desktop_hide table.icons-inner,
        .row-3 .column-1 .block-3.button_block .alignment a,
        .row-3 .column-1 .block-3.button_block .alignment div,
        .social_block.desktop_hide .social-table {
          display: inline-block !important;
        }

        .icons-inner {
          text-align: center;
        }

        .icons-inner td {
          margin: 0 auto;
        }

        .image_block div.fullWidth {
          max-width: 100% !important;
        }

        .mobile_hide {
          display: none;
        }

        .row-content {
          width: 100% !important;
        }

        .stack .column {
          width: 100%;
          display: block;
        }

        .mobile_hide {
          min-height: 0;
          max-height: 0;
          max-width: 0;
          overflow: hidden;
          font-size: 0px;
        }

        .desktop_hide,
        .desktop_hide table {
          display: table !important;
          max-height: none !important;
        }

        .row-1 .column-1 .block-1.paragraph_block td.pad > div {
          text-align: center !important;
          font-size: 18px !important;
        }

        .row-3 .column-1 .block-1.heading_block h1,
        .row-3 .column-1 .block-3.button_block .alignment {
          text-align: left !important;
        }

        .row-3 .column-1 .block-1.heading_block h1 {
          font-size: 20px !important;
        }

        .row-3 .column-1 .block-2.paragraph_block td.pad > div {
          text-align: left !important;
          font-size: 14px !important;
        }

        .row-3 .column-1 .block-3.button_block a,
        .row-3 .column-1 .block-3.button_block div,
        .row-3 .column-1 .block-3.button_block span {
          font-size: 14px !important;
          line-height: 28px !important;
        }

        .row-3 .column-1 .block-4.paragraph_block td.pad > div {
          text-align: justify !important;
          font-size: 10px !important;
        }

        .row-4 .column-1 .block-1.icons_block td.pad {
          text-align: center !important;
          padding: 10px 24px !important;
        }

        .row-4 .column-2 .block-1.paragraph_block td.pad > div {
          text-align: left !important;
          font-size: 16px !important;
        }

        .row-6 .column-1 .block-1.paragraph_block td.pad {
          padding: 0 0 16px !important;
        }

        .row-6 .column-1 .block-2.menu_block .alignment {
          text-align: center !important;
        }

        .row-6 .column-1 .block-2.menu_block td.pad {
          padding: 8px !important;
        }

        .row-6 .column-1 .block-2.menu_block .menu-links a,
        .row-6 .column-1 .block-2.menu_block .menu-links span {
          font-size: 14px !important;
        }

        .row-3 .column-1 {
          padding: 0 24px 48px !important;
        }

        .row-4 .column-1 {
          padding: 16px 16px 8px !important;
        }

        .row-4 .column-2 {
          padding: 0 24px 16px !important;
        }

        .row-6 .column-1 {
          padding: 32px 16px 48px !important;
        }
      }
    </style>
    <!--[if mso
      ]><style>
        sup,
        sub {
          font-size: 100% !important;
        }
        sup {
          mso-text-raise: 10%;
        }
        sub {
          mso-text-raise: -10%;
        }
      </style>
    <![endif]-->
  </head>
  <body
    class="body"
    style="
      background-color: #f8f6ff;
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: none;
      text-size-adjust: none;
    "
  >
    <table
      border="0"
      cellpadding="0"
      cellspacing="0"
      class="nl-container"
      role="presentation"
      style="
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        background-color: #f8f6ff;
        background-image: none;
        background-position: top left;
        background-size: auto;
        background-repeat: no-repeat;
      "
      width="100%"
    >
      <tbody>
        <tr>
          <td>
            <table
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              class="row row-1"
              role="presentation"
              style="mso-table-lspace: 0pt; mso-table-rspace: 0pt"
              width="100%"
            >
              <tbody>
                <tr>
                  <td>
                    <table
                      align="center"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      class="row-content stack"
                      role="presentation"
                      style="
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                        background-color: #a797ff;
                        color: #000000;
                        width: 680px;
                        margin: 0 auto;
                      "
                      width="680"
                    >
                      <tbody>
                        <tr>
                          <td
                            class="column column-1"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              padding-bottom: 32px;
                              padding-left: 48px;
                              padding-right: 48px;
                              padding-top: 32px;
                              vertical-align: top;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="100%"
                          >
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="paragraph_block block-1"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                word-break: break-word;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad">
                                  <div
                                    style="
                                      color: #ffffff;
                                      direction: ltr;
                                      font-family: Helvetica Neue, Helvetica,
                                        Arial, sans-serif;
                                      font-size: 24px;
                                      font-weight: 700;
                                      letter-spacing: 0px;
                                      line-height: 120%;
                                      text-align: left;
                                      mso-line-height-alt: 28.799999999999997px;
                                    "
                                  >
                                    <p style="margin: 0">' . siteName . '</p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              class="row row-2"
              role="presentation"
              style="mso-table-lspace: 0pt; mso-table-rspace: 0pt"
              width="100%"
            >
              <tbody>
                <tr>
                  <td>
                    <table
                      align="center"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      class="row-content stack"
                      role="presentation"
                      style="
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                        background-color: #a797ff;
                        border-radius: 0;
                        color: #000000;
                        width: 680px;
                        margin: 0 auto;
                      "
                      width="680"
                    >
                      <tbody>
                        <tr>
                          <td
                            class="column column-1"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              vertical-align: top;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="100%"
                          >
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="image_block block-1"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                              "
                              width="100%"
                            >
                              <tr>
                                <td
                                  class="pad"
                                  style="
                                    width: 100%;
                                    padding-right: 0px;
                                    padding-left: 0px;
                                  "
                                >
                                  <div
                                    align="center"
                                    class="alignment"
                                    style="line-height: 10px"
                                  >
                                    <div
                                      class="fullWidth"
                                      style="max-width: 640px"
                                    >
                                      <img
                                        alt="An open email illustration"
                                        height="auto"
                                        src="' . siteLink . '/images/Email-Illustration.png"
                                        style="
                                          display: block;
                                          height: auto;
                                          border: 0;
                                          width: 100%;
                                        "
                                        title="An open email illustration"
                                        width="640"
                                      />
                                    </div>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              class="row row-3"
              role="presentation"
              style="mso-table-lspace: 0pt; mso-table-rspace: 0pt"
              width="100%"
            >
              <tbody>
                <tr>
                  <td>
                    <table
                      align="center"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      class="row-content stack"
                      role="presentation"
                      style="
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                        background-color: #ffffff;
                        border-radius: 0;
                        color: #000000;
                        width: 680px;
                        margin: 0 auto;
                      "
                      width="680"
                    >
                      <tbody>
                        <tr>
                          <td
                            class="column column-1"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              padding-bottom: 48px;
                              padding-left: 48px;
                              padding-right: 48px;
                              vertical-align: top;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="100%"
                          >
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="heading_block block-1"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                              "
                              width="100%"
                            >
                              <tr>
                                <td
                                  class="pad"
                                  style="
                                    padding-top: 12px;
                                    text-align: center;
                                    width: 100%;
                                  "
                                >
                                  <h1
                                    style="
                                      margin: 0;
                                      color: #292929;
                                      direction: ltr;
                                      font-family: \'Helvetica Neue\', Helvetica,
                                        Arial, sans-serif;
                                      font-size: 32px;
                                      font-weight: 700;
                                      letter-spacing: normal;
                                      line-height: 120%;
                                      text-align: left;
                                      margin-top: 0;
                                      margin-bottom: 0;
                                      mso-line-height-alt: 38.4px;
                                    "
                                  >
                                    <span
                                      class="tinyMce-placeholder"
                                      style="word-break: break-word"
                                      >Confirm your email!</span
                                    >
                                  </h1>
                                </td>
                              </tr>
                            </table>
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="paragraph_block block-2"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                word-break: break-word;
                              "
                              width="100%"
                            >
                              <tr>
                                <td
                                  class="pad"
                                  style="
                                    padding-bottom: 10px;
                                    padding-top: 10px;
                                  "
                                >
                                  <div
                                    style="
                                      color: #101112;
                                      direction: ltr;
                                      font-family: \'Helvetica Neue\', Helvetica,
                                        Arial, sans-serif;
                                      font-size: 16px;
                                      font-weight: 400;
                                      letter-spacing: 0px;
                                      line-height: 120%;
                                      text-align: left;
                                      mso-line-height-alt: 19.2px;
                                    "
                                  >
                                    <p style="margin: 0">' . $messageInfo . '</p>
                                    <p style="margin: 0">Confirmation code: <b>' . $token . '</b></p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="button_block block-3"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                              "
                              width="100%"
                            >
                              <tr>
                                <td
                                  class="pad"
                                  style="padding-top: 24px; text-align: left"
                                >
                                  <div align="left" class="alignment">
                                    <a
                                      href="' . siteLink . '/emailConfirmation?email' . $user_data->getEmail() . '&token=' . $token . '"
                                      target="_blank"
                                      style="
                                        background-color: #7259ff;
                                        border-bottom: 0px solid transparent;
                                        border-left: 0px solid transparent;
                                        border-radius: 8px;
                                        border-right: 0px solid transparent;
                                        border-top: 0px solid transparent;
                                        color: #ffffff;
                                        display: inline-block;
                                        font-family: Helvetica Neue, Helvetica,
                                          Arial, sans-serif;
                                        font-size: 16px;
                                        font-weight: 400;
                                        mso-border-alt: none;
                                        padding-bottom: 8px;
                                        padding-top: 8px;
                                        text-align: center;
                                        text-decoration: none;
                                        width: auto;
                                        word-break: keep-all;
                                      "
                                      target="_blank"
                                      ><span
                                        style="
                                          word-break: break-word;
                                          padding-left: 16px;
                                          padding-right: 16px;
                                          font-size: 16px;
                                          display: inline-block;
                                          letter-spacing: normal;
                                        "
                                        ><span
                                          style="
                                            word-break: break-word;
                                            line-height: 32px;
                                          "
                                          >Confirm subscription</span
                                        ></span
                                      ></a
                                    >
                                  </div>
                                </td>
                              </tr>
                            </table>
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="paragraph_block block-4"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                word-break: break-word;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad" style="padding-top: 16px">
                                  <div
                                    style="
                                      color: #666666;
                                      direction: ltr;
                                      font-family: \'Helvetica Neue\', Helvetica,
                                        Arial, sans-serif;
                                      font-size: 12px;
                                      font-weight: 400;
                                      letter-spacing: 0px;
                                      line-height: 120%;
                                      text-align: left;
                                      mso-line-height-alt: 14.399999999999999px;
                                    "
                                  >
                                    <p style="margin: 0">
                                      By confirming your subscription, you\'ll be
                                      joining our news letter list, you can
                                      always leave the email list any time.
                                    </p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              class="row row-4"
              role="presentation"
              style="mso-table-lspace: 0pt; mso-table-rspace: 0pt"
              width="100%"
            >
              <tbody>
                <tr>
                  <td>
                    <table
                      align="center"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      class="row-content stack"
                      role="presentation"
                      style="
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                        background-color: #e8e4ff;
                        border-left: 20px solid #ffffff;
                        border-radius: 0;
                        border-right: 20px solid #ffffff;
                        color: #000000;
                        width: 680px;
                        margin: 0 auto;
                      "
                      width="680"
                    >
                      <tbody>
                        <tr>
                          <td
                            class="column column-1"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              padding-bottom: 24px;
                              padding-left: 8px;
                              padding-right: 8px;
                              padding-top: 24px;
                              vertical-align: middle;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="16.666666666666668%"
                          >
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="icons_block block-1"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                text-align: center;
                                line-height: 0;
                              "
                              width="100%"
                            >
                              <tr>
                                <td
                                  class="pad"
                                  style="
                                    vertical-align: middle;
                                    color: #000000;
                                    font-family: inherit;
                                    font-size: 14px;
                                    font-weight: 400;
                                    text-align: center;
                                  "
                                >
                                  <table
                                    cellpadding="0"
                                    cellspacing="0"
                                    class="icons-outer"
                                    role="presentation"
                                    style="
                                      mso-table-lspace: 0pt;
                                      mso-table-rspace: 0pt;
                                      display: inline-table;
                                    "
                                  >
                                    <tr>
                                      <td
                                        style="
                                          vertical-align: middle;
                                          text-align: center;
                                          padding-top: 0px;
                                          padding-bottom: 0px;
                                          padding-left: 0px;
                                          padding-right: 0px;
                                        "
                                      >
                                        <img
                                          align="center"
                                          class="icon"
                                          height="auto"
                                          src="' . siteLink . '/images/Gift-Emoji.png"
                                          style="
                                            display: block;
                                            height: auto;
                                            margin: 0 auto;
                                            border: 0;
                                          "
                                          width="34"
                                        />
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                          <td
                            class="column column-2"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              padding-bottom: 5px;
                              padding-right: 48px;
                              padding-top: 5px;
                              vertical-align: middle;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="83.33333333333333%"
                          >
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="paragraph_block block-1"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                word-break: break-word;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad">
                                  <div
                                    style="
                                      color: #7860ff;
                                      direction: ltr;
                                      font-family: Helvetica Neue, Helvetica,
                                        Arial, sans-serif;
                                      font-size: 16px;
                                      font-weight: 400;
                                      letter-spacing: 0px;
                                      line-height: 150%;
                                      text-align: left;
                                      mso-line-height-alt: 24px;
                                    "
                                  >
                                    <p style="margin: 0">
                                      Invite your friends to our community and
                                      earn credits to win gifts <br /><strong
                                        ><a
                                          href="' . siteLink . '/register?ref=' . $user_data->getEmail() . '"
                                          rel="noopener"
                                          style="
                                            text-decoration: underline;
                                            color: #3e2d9c;
                                          "
                                          target="_blank"
                                          >Share with Friends</a
                                        ></strong
                                      >
                                    </p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              class="row row-5"
              role="presentation"
              style="mso-table-lspace: 0pt; mso-table-rspace: 0pt"
              width="100%"
            >
              <tbody>
                <tr>
                  <td>
                    <table
                      align="center"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      class="row-content stack"
                      role="presentation"
                      style="
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                        background-color: #ffffff;
                        border-radius: 0;
                        color: #000000;
                        width: 680px;
                        margin: 0 auto;
                      "
                      width="680"
                    >
                      <tbody>
                        <tr>
                          <td
                            class="column column-1"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              vertical-align: top;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="100%"
                          >
                            <div
                              class="spacer_block block-1"
                              style="
                                height: 56px;
                                line-height: 56px;
                                font-size: 1px;
                              "
                            ></div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              class="row row-6"
              role="presentation"
              style="mso-table-lspace: 0pt; mso-table-rspace: 0pt"
              width="100%"
            >
              <tbody>
                <tr>
                  <td>
                    <table
                      align="center"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      class="row-content stack"
                      role="presentation"
                      style="
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                        background-color: #a797ff;
                        border-radius: 0;
                        color: #000000;
                        width: 680px;
                        margin: 0 auto;
                      "
                      width="680"
                    >
                      <tbody>
                        <tr>
                          <td
                            class="column column-1"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              padding-bottom: 48px;
                              padding-top: 32px;
                              vertical-align: top;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="100%"
                          >
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="paragraph_block block-1"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                word-break: break-word;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad" style="padding-bottom: 32px">
                                  <div
                                    style="
                                      color: #ffffff;
                                      direction: ltr;
                                      font-family: Helvetica Neue, Helvetica,
                                        Arial, sans-serif;
                                      font-size: 24px;
                                      font-weight: 700;
                                      letter-spacing: 0px;
                                      line-height: 120%;
                                      text-align: center;
                                      mso-line-height-alt: 28.799999999999997px;
                                    "
                                  >
                                    <p style="margin: 0">
                                      <img src="' . siteLink . '/images/logo2.png" height="45px" width="45px"/>
                                    </p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                            <table
                              border="0"
                              cellpadding="8"
                              cellspacing="0"
                              class="menu_block block-2"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad">
                                  <table
                                    border="0"
                                    cellpadding="0"
                                    cellspacing="0"
                                    role="presentation"
                                    style="
                                      mso-table-lspace: 0pt;
                                      mso-table-rspace: 0pt;
                                    "
                                    width="100%"
                                  >
                                    <tr>
                                      <td
                                        class="alignment"
                                        style="
                                          text-align: center;
                                          font-size: 0px;
                                        "
                                      >
                                        <div class="menu-links">
                                          <!--[if mso]><table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style=""><tr style="text-align:center;"><!
                                          [endif]-->[endif]--><!--[if mso]><td style="padding-top:0px;padding-right:16px;padding-bottom:0px;padding-left:16px"><!
                                          [endif]--><a
                                            href="' . siteLink . '"
                                            style="
                                              mso-hide: false;
                                              padding-top: 0px;
                                              padding-bottom: 0px;
                                              padding-left: 16px;
                                              padding-right: 16px;
                                              display: inline-block;
                                              color: #3e2d9c;
                                              font-family:\'Helvetica Neue\',
                                                Helvetica, Arial, sans-serif;
                                              font-size: 16px;
                                              text-decoration: none;
                                              letter-spacing: 1px;
                                            "
                                            target="_self"
                                            >Home</a
                                          ><span
                                            class="sep"
                                            style="
                                              word-break: break-word;
                                              font-size: 16px;
                                              font-family: \'Helvetica Neue\',
                                                Helvetica, Arial, sans-serif;
                                              color: #3e2d9c;
                                            "
                                            >|</span
                                          >
                                          <a
                                            href="' . siteLink . '/"
                                            style="
                                              mso-hide: false;
                                              padding-top: 0px;
                                              padding-bottom: 0px;
                                              padding-left: 16px;
                                              padding-right: 16px;
                                              display: inline-block;
                                              color: #3e2d9c;
                                              font-family: \'Helvetica Neue\',
                                                Helvetica, Arial, sans-serif;
                                              font-size: 16px;
                                              text-decoration: none;
                                              letter-spacing: 1px;
                                            "
                                            target="_self"
                                            >About us</a
                                          >><!--[if mso]></td><td><!
                                          [endif]--><span
                                            class="sep"
                                            style="
                                              word-break: break-word;
                                              font-size: 16px;
                                              font-family: \'Helvetica Neue\',
                                                Helvetica, Arial, sans-serif;
                                              color: #3e2d9c;
                                            "
                                            >|</span
                                          >
                                          <a
                                            href="mailto:' . supportMail . '"
                                            style="
                                              mso-hide: false;
                                              padding-top: 0px;
                                              padding-bottom: 0px;
                                              padding-left: 16px;
                                              padding-right: 16px;
                                              display: inline-block;
                                              color: #3e2d9c;
                                              font-family: \'Helvetica Neue\',
                                                Helvetica, Arial, sans-serif;
                                              font-size: 16px;
                                              text-decoration: none;
                                              letter-spacing: 1px;
                                            "
                                            target="_self"
                                            >Contact us</a
                                          >
                                        </div>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                            <!-- <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="social_block block-3"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                              "
                              width="100%"
                            >
                              <tr>
                                <td
                                  class="pad"
                                  style="
                                    padding-bottom: 32px;
                                    padding-top: 24px;
                                    text-align: center;
                                    padding-right: 0px;
                                    padding-left: 0px;
                                  "
                                >
                                  <div align="center" class="alignment">
                                    <table
                                      border="0"
                                      cellpadding="0"
                                      cellspacing="0"
                                      class="social-table"
                                      role="presentation"
                                      style="
                                        mso-table-lspace: 0pt;
                                        mso-table-rspace: 0pt;
                                        display: inline-block;
                                      "
                                      width="184px"
                                    >
                                      <tr>
                                        <td style="padding: 0 7px 0 7px">
                                          <a
                                            href="https://www.facebook.com/"
                                            target="_blank"
                                            ><img
                                              alt="Facebook"
                                              height="auto"
                                              src="images/facebook2x.png"
                                              style="
                                                display: block;
                                                height: auto;
                                                border: 0;
                                              "
                                              title="facebook"
                                              width="32"
                                          /></a>
                                        </td>
                                        <td style="padding: 0 7px 0 7px">
                                          <a
                                            href="https://www.twitter.com/"
                                            target="_blank"
                                            ><img
                                              alt="Twitter"
                                              height="auto"
                                              src="images/twitter2x.png"
                                              style="
                                                display: block;
                                                height: auto;
                                                border: 0;
                                              "
                                              title="twitter"
                                              width="32"
                                          /></a>
                                        </td>
                                        <td style="padding: 0 7px 0 7px">
                                          <a
                                            href="https://www.linkedin.com/"
                                            target="_blank"
                                            ><img
                                              alt="Linkedin"
                                              height="auto"
                                              src="images/linkedin2x.png"
                                              style="
                                                display: block;
                                                height: auto;
                                                border: 0;
                                              "
                                              title="linkedin"
                                              width="32"
                                          /></a>
                                        </td>
                                        <td style="padding: 0 7px 0 7px">
                                          <a
                                            href="https://www.instagram.com/"
                                            target="_blank"
                                            ><img
                                              alt="Instagram"
                                              height="auto"
                                              src="images/instagram2x.png"
                                              style="
                                                display: block;
                                                height: auto;
                                                border: 0;
                                              "
                                              title="instagram"
                                              width="32"
                                          /></a>
                                        </td>
                                      </tr>
                                    </table>
                                  </div>
                                </td>
                              </tr>
                            </table> -->
                            <table
                              border="0"
                              cellpadding="10"
                              cellspacing="0"
                              class="divider_block block-4"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad">
                                  <div align="center" class="alignment">
                                    <table
                                      border="0"
                                      cellpadding="0"
                                      cellspacing="0"
                                      role="presentation"
                                      style="
                                        mso-table-lspace: 0pt;
                                        mso-table-rspace: 0pt;
                                      "
                                      width="85%"
                                    >
                                      <tr>
                                        <td
                                          class="divider_inner"
                                          style="
                                            font-size: 1px;
                                            line-height: 1px;
                                            border-top: 1px solid #9583ff;
                                          "
                                        >
                                          <span style="word-break: break-word"
                                            > </span
                                          >
                                        </td>
                                      </tr>
                                    </table>
                                  </div>
                                </td>
                              </tr>
                            </table>
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="paragraph_block block-5"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                word-break: break-word;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad" style="padding-top: 16px">
                                  <div
                                    style="
                                      color: #443888;
                                      direction: ltr;
                                      font-family: \'Helvetica Neue\', Helvetica,
                                        Arial, sans-serif;
                                      font-size: 12px;
                                      font-weight: 400;
                                      letter-spacing: 0px;
                                      line-height: 120%;
                                      text-align: center;
                                      mso-line-height-alt: 14.399999999999999px;
                                    "
                                  >
                                    <p style="margin: 0">
                                      You have received this email because you
                                      are a subscriber of
                                      <a
                                        href="' . siteLink . '"
                                        rel="noopener"
                                        style="
                                          text-decoration: underline;
                                          color: #3e2d9c;
                                        "
                                        target="_blank"
                                        >' . siteDomain . '</a
                                      >
                                    </p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="paragraph_block block-6"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                word-break: break-word;
                              "
                              width="100%"
                            >
                              <tr>
                                <td class="pad" style="padding-top: 16px">
                                  <div
                                    style="
                                      color: #443888;
                                      direction: ltr;
                                      font-family: \'Helvetica Neue\', Helvetica,
                                        Arial, sans-serif;
                                      font-size: 12px;
                                      font-weight: 400;
                                      letter-spacing: 0px;
                                      line-height: 120%;
                                      text-align: center;
                                      mso-line-height-alt: 14.399999999999999px;
                                    "
                                  >
                                    <p style="margin: 0">
                                      if you feel you received it by mistake or
                                      wish to unsubscribe,
                                      <a
                                        href="' . siteLink . '/unsubcribe"
                                        rel="noopener"
                                        style="
                                          text-decoration: underline;
                                          color: #3e2d9c;
                                        "
                                        target="_blank"
                                        >click here</a
                                      >.
                                    </p>
                                  </div>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <table
              align="center"
              border="0"
              cellpadding="0"
              cellspacing="0"
              class="row row-7"
              role="presentation"
              style="
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                background-color: #ffffff;
              "
              width="100%"
            >
              <tbody>
                <tr>
                  <td>
                    <table
                      align="center"
                      border="0"
                      cellpadding="0"
                      cellspacing="0"
                      class="row-content stack"
                      role="presentation"
                      style="
                        mso-table-lspace: 0pt;
                        mso-table-rspace: 0pt;
                        background-color: #ffffff;
                        color: #000000;
                        width: 680px;
                        margin: 0 auto;
                      "
                      width="680"
                    >
                      <tbody>
                        <tr>
                          <td
                            class="column column-1"
                            style="
                              mso-table-lspace: 0pt;
                              mso-table-rspace: 0pt;
                              font-weight: 400;
                              text-align: left;
                              padding-bottom: 5px;
                              padding-top: 5px;
                              vertical-align: top;
                              border-top: 0px;
                              border-right: 0px;
                              border-bottom: 0px;
                              border-left: 0px;
                            "
                            width="100%"
                          >
                            <table
                              border="0"
                              cellpadding="0"
                              cellspacing="0"
                              class="icons_block block-1"
                              role="presentation"
                              style="
                                mso-table-lspace: 0pt;
                                mso-table-rspace: 0pt;
                                text-align: center;
                                line-height: 0;
                              "
                              width="100%"
                            >
                              <tr>
                                <td
                                  class="pad"
                                  style="
                                    vertical-align: middle;
                                    color: #1e0e4b;
                                    font-family: \'Inter\', sans-serif;
                                    font-size: 15px;
                                    padding-bottom: 5px;
                                    padding-top: 5px;
                                    text-align: center;
                                  "
                                >
                                  <!--[if vml]><table align="center" cellpadding="0" cellspacing="0" role="presentation" style="display:inline-block;padding-left:0px;padding-right:0px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;"><![endif]-->
                                  <!--[if !vml]><!-->
                                  <table
                                    cellpadding="0"
                                    cellspacing="0"
                                    class="icons-inner"
                                    role="presentation"
                                    style="
                                      mso-table-lspace: 0pt;
                                      mso-table-rspace: 0pt;
                                      display: inline-block;
                                      padding-left: 0px;
                                      padding-right: 0px;
                                    "
                                  ></table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- End -->
  </body>
</html>
';

    return $message;
  }

  public static function adminSendMail($sender, $messageInfo)
  {
    $message =
      '
<!DOCTYPE html>
<html>

<head>
<title>Email From ' . siteName . '</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<style type="text/css">
';

    $message .=
      "
@media screen {
@font-face {
font-family: 'Lato';
font-style: normal;
font-weight: 400;
src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
}

@font-face {
font-family: 'Lato';
font-style: normal;
font-weight: 700;
src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
}

@font-face {
font-family: 'Lato';
font-style: italic;
font-weight: 400;
src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
}

@font-face {
font-family: 'Lato';
font-style: italic;
font-weight: 700;
src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
}
}

/* CLIENT-SPECIFIC STYLES */
body,
table,
td,
a {
-webkit-text-size-adjust: 100%;
-ms-text-size-adjust: 100%;
}

table,
td {
mso-table-lspace: 0pt;
mso-table-rspace: 0pt;
}

img {
-ms-interpolation-mode: bicubic;
}

/* RESET STYLES */
img {
border: 0;
height: auto;
line-height: 100%;
outline: none;
text-decoration: none;
}

table {
border-collapse: collapse !important;
}

body {
height: 100% !important;
margin: 0 !important;
padding: 0 !important;
width: 100% !important;
}

/* iOS BLUE LINKS */
a[x-apple-data-detectors] {
color: inherit !important;
text-decoration: none !important;
font-size: inherit !important;
font-family: inherit !important;
font-weight: inherit !important;
line-height: inherit !important;
}

/* MOBILE STYLES */
@media screen and (max-width:600px) {
h1 {
font-size: 32px !important;
line-height: 32px !important;
}
}


";

    $message .=
      '
/* ANDROID CENTER FIX */
div[style*="margin: 16px 0;"] {
margin: 0 !important;
}
</style>
</head>
<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: \'Lato\', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;"> We\'re thrilled to have you here! Get ready to dive into your new account. </div>
';

    $message .=
      '
<!-- HIDDEN PREHEADER TEXT -->

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<!-- LOGO -->
<tr>
<td bgcolor="#FFA73B" align="center">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
<tr>
<td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
</tr>
</table>
</td>
</tr>
<tr>
<td bgcolor="#FFA73B" align="center" style="padding: 0px 10px 0px 10px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
<tr>
<td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: \'Lato\', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
    <a href="' . siteLink . '"><img src="' . siteLink . '/images/logo.jpg" width="145" height="160" style="display: block; border: 0px;" /></a>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
<tr>
<td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: \'Lato\', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
';

    $message .= $messageInfo;
    $message .= '
<table>
<tr>
<td bgcolor="#ffffff" align="left" style="padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #666666; font-family: \'Lato\', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
    <p style="margin: 0;">Kind regards,<br>' . siteName . ' Team</p>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
<tr>
<td bgcolor="#FFECD1" align="center" style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: \'Lato\', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
    <h2 style="font-size: 20px; font-weight: 400; color: #111111; margin: 0;">Need more help?</h2>
    <p style="margin: 0;"><a href="' . siteLink . '" target="_blank" style="color: #FFA73B;">We&rsquo;re here to help you out</a></p>
    <p style="margin: 0;">
    Regards <a href="mailto:' . $sender . '" style="color: #FFA73B;">' . $sender . '</a>
    <!-- <br>
    WhatsApp <a href="https://wa.me/' . sitePhone . '" style="color: #FFA73B;">https://wa.me/' . sitePhone . '</a> -->
    </p>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
<tr>
<td bgcolor="#f4f4f4" align="left" style="padding: 0px 30px 30px 30px; color: #666666; font-family: \'Lato\', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;"> <br>
    <p style="margin: 0;"></p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>

</html>
';

    return $message;
  }

  public static function setOtpSuccessMail(string $userName, string $title = "Account Verified Successfully"): string
  {
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <title>' . htmlspecialchars($title) . '</title>
    </head>
    <body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#333;">
      <center style="width:100%;background:#f4f4f7;padding:20px 0;">
        <div style="max-width:480px;margin:0 auto;background:#fff;border-radius:8px;
          overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05);">

          <!-- HEADER -->
          <div style="background:#30e3ca;padding:15px;text-align:center;">
            <h2 style="margin:0;font-size:20px;color:#fff;font-weight:600;">' . htmlspecialchars($title) . '</h2>
          </div>

          <!-- BODY -->
          <div style="padding:30px;text-align:center;line-height:1.6;">
            <p style="margin:0 0 15px;font-size:16px;color:#444;">
              Hi <strong>' . htmlspecialchars($userName) . '</strong>,
            </p>
            <p style="margin:0 0 15px;font-size:16px;color:#444;">
              🎉 Congratulations! Your email has been successfully verified.
            </p>
            <p style="margin:0;font-size:14px;color:#666;">
              You can now enjoy full access to all features on <strong>' . siteName . '</strong>.
            </p>
          </div>

          <!-- CTA BUTTON -->
          <div style="padding:20px;text-align:center;">
            <a href="' . siteLink . '" 
              style="background:#30e3ca;color:#fff;text-decoration:none;padding:12px 24px;
              border-radius:5px;font-size:16px;display:inline-block;">
              Go to Dashboard
            </a>
          </div>

          <!-- FOOTER -->
          <div style="background:#fafafa;padding:15px;text-align:center;font-size:13px;color:#999;">
            <p style="margin:0;">If you didn’t verify this account, please contact us immediately.</p>
            <p style="margin:10px 0 0;">&copy; ' . date("Y") . ' ' . siteName . '</p>
          </div>

        </div>
      </center>
    </body>
    </html>';

    return $message;
  }

  public static function setTransactionMail(string $title, string $description, array $transaction): string
  {
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <title>' . htmlspecialchars($title) . '</title>
    </head>
    <body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#333;">
      <center style="width:100%;background:#f4f4f7;padding:20px 0;">
        <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;
          overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05);">

          <!-- HEADER -->
          <div style="background:#30e3ca;padding:15px;text-align:center;">
            <h2 style="margin:0;font-size:20px;color:#fff;font-weight:600;">' . htmlspecialchars($title) . '</h2>
          </div>

          <!-- DESCRIPTION -->
          <div style="padding:20px;text-align:center;line-height:1.6;">
            <p style="margin:0 0 15px;font-size:16px;color:#444;">
              ' . nl2br(htmlspecialchars($description)) . '
            </p>
          </div>

          <!-- TRANSACTION DETAILS -->
          <div style="padding:20px;">
            <table width="100%" cellpadding="8" cellspacing="0" 
              style="border-collapse:collapse;font-size:15px;color:#333;">
              <tr style="background:#f9f9f9;">
                <td style="font-weight:bold;width:40%;">Transaction ID</td>
                <td>' . htmlspecialchars($transaction["id"] ?? "") . '</td>
              </tr>
              <tr>
                <td style="font-weight:bold;">Date</td>
                <td>' . htmlspecialchars($transaction["date"] ?? "") . '</td>
              </tr>
              <tr style="background:#f9f9f9;">
                <td style="font-weight:bold;">Amount</td>
                <td>' . htmlspecialchars($transaction["amount"] ?? "") . '</td>
              </tr>
              <tr>
                <td style="font-weight:bold;">Transaction Type</td>
                <td>' . htmlspecialchars($transaction["type"] ?? "") . '</td>
              </tr>
              <tr style="background:#f9f9f9;">
                <td style="font-weight:bold;">Total</td>
                <td><strong>' . htmlspecialchars($transaction["total"] ?? "") . '</strong></td>
              </tr>
            </table>
          </div>

          <!-- FOOTER -->
          <div style="background:#fafafa;padding:15px;text-align:center;font-size:13px;color:#999;">
            <p style="margin:0;">If you have any questions, contact us at 
              <a href="mailto:support@' . siteDomain . '" style="color:#30e3ca;">support@' . siteDomain . '</a>
            </p>
            <p style="margin:10px 0 0;">&copy; ' . date("Y") . ' ' . siteName . '</p>
          </div>

        </div>
      </center>
    </body>
    </html>';

    return $message;
  }

}