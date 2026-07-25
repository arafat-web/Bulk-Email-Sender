<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to Our Platform!',
                'body' => '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
  <tr><td style="background:#0f172a;padding:40px 40px 32px;text-align:center;">
    <h1 style="color:#fff;font-size:26px;margin:0;font-weight:700;">Welcome Aboard!</h1>
  </td></tr>
  <tr><td style="padding:36px 40px;">
    <p style="font-size:15px;color:#334155;line-height:1.7;margin:0 0 16px;">Hi there,</p>
    <p style="font-size:15px;color:#334155;line-height:1.7;margin:0 0 16px;">We\'re thrilled to have you join us. Your account is all set up and ready to go.</p>
    <p style="font-size:15px;color:#334155;line-height:1.7;margin:0 0 24px;">Here are a few things you can do to get started:</p>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
      <tr><td style="padding:14px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #0f172a;margin-bottom:8px;">
        <span style="font-size:14px;color:#0f172a;font-weight:600;">&#10003;</span>
        <span style="font-size:14px;color:#334155;margin-left:8px;">Complete your profile</span>
      </td></tr>
      <tr><td height="8"></td></tr>
      <tr><td style="padding:14px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #0f172a;">
        <span style="font-size:14px;color:#0f172a;font-weight:600;">&#10003;</span>
        <span style="font-size:14px;color:#334155;margin-left:8px;">Explore your dashboard</span>
      </td></tr>
      <tr><td height="8"></td></tr>
      <tr><td style="padding:14px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #0f172a;">
        <span style="font-size:14px;color:#0f172a;font-weight:600;">&#10003;</span>
        <span style="font-size:14px;color:#334155;margin-left:8px;">Connect with the community</span>
      </td></tr>
    </table>
    <table cellpadding="0" cellspacing="0" style="margin:0 auto 24px;">
      <tr><td align="center" style="background:#0f172a;border-radius:8px;padding:14px 36px;">
        <a href="#" style="color:#fff;text-decoration:none;font-size:14px;font-weight:600;">Get Started</a>
      </td></tr>
    </table>
    <p style="font-size:14px;color:#64748b;line-height:1.6;margin:0;">If you have any questions, just reply to this email.</p>
  </td></tr>
  <tr><td style="background:#f8fafc;padding:24px 40px;text-align:center;border-top:1px solid #e2e8f0;">
    <p style="font-size:12px;color:#94a3b8;margin:0;">&copy; 2026 BulkMailer. All rights reserved.</p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>',
                'description' => 'A warm welcome email with checklist and CTA',
                'is_active' => true,
            ],
            [
                'name' => 'Newsletter',
                'subject' => 'Monthly Newsletter',
                'body' => '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
  <tr><td style="background:#0f172a;padding:36px 40px;text-align:center;">
    <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;">Monthly Digest</div>
    <h2 style="color:#fff;font-size:24px;margin:0;font-weight:700;">What\'s New This Month</h2>
  </td></tr>
  <tr><td style="padding:32px 40px;">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr><td style="padding-bottom:20px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc;border-radius:10px;">
          <tr><td style="padding:20px;">
            <h3 style="font-size:16px;color:#0f172a;margin:0 0 6px;font-weight:600;">Feature Update</h3>
            <p style="font-size:14px;color:#475569;line-height:1.6;margin:0;">We\'ve shipped several improvements to make your workflow faster.</p>
          </td></tr>
        </table>
      </td></tr>
      <tr><td style="padding-bottom:20px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc;border-radius:10px;">
          <tr><td style="padding:20px;">
            <h3 style="font-size:16px;color:#0f172a;margin:0 0 6px;font-weight:600;">Upcoming Webinar</h3>
            <p style="font-size:14px;color:#475569;line-height:1.6;margin:0;">Join us for a live session on best practices. Save your spot!</p>
          </td></tr>
        </table>
      </td></tr>
      <tr><td style="padding-bottom:20px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="background:#f8fafc;border-radius:10px;">
          <tr><td style="padding:20px;">
            <h3 style="font-size:16px;color:#0f172a;margin:0 0 6px;font-weight:600;">Community Spotlight</h3>
            <p style="font-size:14px;color:#475569;line-height:1.6;margin:0;">See how top users are getting results with the platform.</p>
          </td></tr>
        </table>
      </td></tr>
    </table>
    <table cellpadding="0" cellspacing="0" style="margin:8px auto 0;">
      <tr><td align="center" style="background:#0f172a;border-radius:8px;padding:14px 36px;">
        <a href="#" style="color:#fff;text-decoration:none;font-size:14px;font-weight:600;">Read Full Newsletter</a>
      </td></tr>
    </table>
  </td></tr>
  <tr><td style="background:#f8fafc;padding:24px 40px;text-align:center;border-top:1px solid #e2e8f0;">
    <p style="font-size:12px;color:#94a3b8;margin:0 0 4px;">You\'re receiving this because you subscribed.</p>
    <a href="#" style="font-size:12px;color:#64748b;">Unsubscribe</a>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>',
                'description' => 'Clean newsletter layout with article cards',
                'is_active' => true,
            ],
            [
                'name' => 'Product Launch',
                'subject' => 'Introducing Our New Product',
                'body' => '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
  <tr><td style="background:#0f172a;padding:48px 40px;text-align:center;">
    <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;">Just Launched</div>
    <h1 style="color:#fff;font-size:30px;margin:0 0 10px;font-weight:700;">Meet the New Standard</h1>
    <p style="color:#cbd5e1;font-size:15px;margin:0;line-height:1.5;">Powerful. Intuitive. Built for you.</p>
  </td></tr>
  <tr><td style="padding:40px;">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr><td style="padding-bottom:24px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:24px;">
          <tr><td style="font-size:28px;text-align:center;padding-bottom:10px;">&#9889;</td></tr>
          <tr><td style="text-align:center;">
            <h3 style="font-size:16px;color:#0f172a;margin:0 0 6px;font-weight:600;">Lightning Fast</h3>
            <p style="font-size:14px;color:#475569;line-height:1.5;margin:0;">Optimized performance for your workflow.</p>
          </td></tr>
        </table>
      </td></tr>
      <tr><td style="padding-bottom:24px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:24px;">
          <tr><td style="font-size:28px;text-align:center;padding-bottom:10px;">&#128274;</td></tr>
          <tr><td style="text-align:center;">
            <h3 style="font-size:16px;color:#0f172a;margin:0 0 6px;font-weight:600;">Enterprise Security</h3>
            <p style="font-size:14px;color:#475569;line-height:1.5;margin:0;">Bank-grade encryption and compliance.</p>
          </td></tr>
        </table>
      </td></tr>
      <tr><td style="padding-bottom:24px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:24px;">
          <tr><td style="font-size:28px;text-align:center;padding-bottom:10px;">&#127919;</td></tr>
          <tr><td style="text-align:center;">
            <h3 style="font-size:16px;color:#0f172a;margin:0 0 6px;font-weight:600;">Results Driven</h3>
            <p style="font-size:14px;color:#475569;line-height:1.5;margin:0;">Built to deliver measurable outcomes.</p>
          </td></tr>
        </table>
      </td></tr>
    </table>
    <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
      <tr><td align="center" style="background:#0f172a;border-radius:8px;padding:14px 40px;">
        <a href="#" style="color:#fff;text-decoration:none;font-size:15px;font-weight:600;">Try It Free</a>
      </td></tr>
    </table>
  </td></tr>
  <tr><td style="background:#f8fafc;padding:24px 40px;text-align:center;border-top:1px solid #e2e8f0;">
    <p style="font-size:12px;color:#94a3b8;margin:0;">&copy; 2026 BulkMailer. All rights reserved.</p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>',
                'description' => 'Bold product launch with feature highlights',
                'is_active' => true,
            ],
            [
                'name' => 'Event Invitation',
                'subject' => 'You\'re Invited!',
                'body' => '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
  <tr><td style="background:#0f172a;padding:40px;text-align:center;">
    <h2 style="color:#fff;font-size:24px;margin:0 0 6px;font-weight:700;">You\'re Invited</h2>
    <p style="color:#cbd5e1;font-size:14px;margin:0;">Join us for an exclusive event</p>
  </td></tr>
  <tr><td style="padding:36px 40px;text-align:center;">
    <h3 style="font-size:20px;color:#0f172a;margin:0 0 20px;font-weight:600;">Annual Conference 2026</h3>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
      <tr><td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;">
        <span style="font-size:13px;color:#64748b;font-weight:500;">Date</span>
        <span style="float:right;font-size:14px;color:#0f172a;font-weight:600;">December 15, 2026</span>
      </td></tr>
      <tr><td style="padding:14px 20px;border-bottom:1px solid #e2e8f0;">
        <span style="font-size:13px;color:#64748b;font-weight:500;">Time</span>
        <span style="float:right;font-size:14px;color:#0f172a;font-weight:600;">10:00 AM - 4:00 PM</span>
      </td></tr>
      <tr><td style="padding:14px 20px;">
        <span style="font-size:13px;color:#64748b;font-weight:500;">Location</span>
        <span style="float:right;font-size:14px;color:#0f172a;font-weight:600;">Grand Convention Center</span>
      </td></tr>
    </table>
    <p style="font-size:14px;color:#475569;line-height:1.6;margin:0 0 24px;">Network with industry leaders, attend workshops, and gain insights to accelerate your growth.</p>
    <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
      <tr><td align="center" style="background:#0f172a;border-radius:8px;padding:14px 36px;">
        <a href="#" style="color:#fff;text-decoration:none;font-size:14px;font-weight:600;">RSVP Now</a>
      </td></tr>
    </table>
  </td></tr>
  <tr><td style="background:#f8fafc;padding:24px 40px;text-align:center;border-top:1px solid #e2e8f0;">
    <p style="font-size:12px;color:#94a3b8;margin:0;">Spots are limited &mdash; reserve yours today.</p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>',
                'description' => 'Elegant event invitation with details layout',
                'is_active' => true,
            ],
            [
                'name' => 'Promotional Offer',
                'subject' => 'Exclusive Offer Just for You',
                'body' => '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
  <tr><td style="background:#0f172a;padding:44px 40px;text-align:center;">
    <div style="font-size:48px;margin-bottom:8px;">&#127873;</div>
    <h2 style="color:#fff;font-size:26px;margin:0 0 6px;font-weight:700;">Special Offer Inside</h2>
    <p style="color:#cbd5e1;font-size:14px;margin:0;">Exclusively for our valued customers</p>
  </td></tr>
  <tr><td style="padding:36px 40px;text-align:center;">
    <div style="display:inline-block;background:#f8fafc;border:2px dashed #0f172a;border-radius:12px;padding:28px 40px;margin-bottom:24px;">
      <p style="font-size:13px;color:#64748b;margin:0 0 4px;text-transform:uppercase;letter-spacing:.06em;">Your Discount Code</p>
      <p style="font-size:32px;color:#0f172a;font-weight:700;margin:0;letter-spacing:2px;">SAVE25</p>
      <p style="font-size:13px;color:#64748b;margin:6px 0 0;">25% off your next purchase</p>
    </div>
    <p style="font-size:14px;color:#475569;line-height:1.6;margin:0 0 20px;">Use this code at checkout to claim your discount. Valid for the next 7 days.</p>
    <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
      <tr><td align="center" style="background:#0f172a;border-radius:8px;padding:14px 36px;">
        <a href="#" style="color:#fff;text-decoration:none;font-size:14px;font-weight:600;">Shop Now</a>
      </td></tr>
    </table>
  </td></tr>
  <tr><td style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
    <p style="font-size:12px;color:#94a3b8;margin:0;">Offer expires in 7 days. Terms apply.</p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>',
                'description' => 'Promotional offer with discount code box',
                'is_active' => true,
            ],
            [
                'name' => 'Thank You',
                'subject' => 'Thank You!',
                'body' => '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;">
  <tr><td style="background:#0f172a;padding:44px 40px;text-align:center;">
    <div style="font-size:44px;margin-bottom:8px;color:#fff;">&#10004;</div>
    <h2 style="color:#fff;font-size:24px;margin:0;font-weight:700;">Thank You!</h2>
  </td></tr>
  <tr><td style="padding:36px 40px;">
    <p style="font-size:15px;color:#334155;line-height:1.7;margin:0 0 16px;">We truly appreciate your business. Your order has been confirmed and is being processed.</p>
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;margin-bottom:20px;">
      <tr><td style="padding:20px 24px;">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td style="padding-bottom:8px;"><span style="font-size:13px;color:#64748b;">Order Number</span></td>
            <td align="right" style="padding-bottom:8px;"><span style="font-size:14px;color:#0f172a;font-weight:600;">#ORD-2026-001</span></td>
          </tr>
          <tr>
            <td><span style="font-size:13px;color:#64748b;">Amount</span></td>
            <td align="right"><span style="font-size:14px;color:#0f172a;font-weight:600;">$99.00</span></td>
          </tr>
        </table>
      </td></tr>
    </table>
    <p style="font-size:14px;color:#475569;line-height:1.6;margin:0 0 20px;">You\'ll receive a confirmation email shortly with your order details.</p>
    <table cellpadding="0" cellspacing="0">
      <tr><td align="center" style="background:#0f172a;border-radius:8px;padding:14px 36px;">
        <a href="#" style="color:#fff;text-decoration:none;font-size:14px;font-weight:600;">View Order</a>
      </td></tr>
    </table>
  </td></tr>
  <tr><td style="background:#f8fafc;padding:24px 40px;text-align:center;border-top:1px solid #e2e8f0;">
    <p style="font-size:12px;color:#94a3b8;margin:0;">Questions? Just reply to this email.</p>
  </td></tr>
</table>
</td></tr>
</table>
</body>
</html>',
                'description' => 'Clean thank you with order summary',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
