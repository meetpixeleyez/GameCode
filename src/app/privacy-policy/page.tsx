import { getPolicyPage, PolicyLayout } from "@/components/policy/policy-layout";

export const metadata = {
  title: "Privacy Policy",
  description: "How Ready Game Code collects, uses, and protects your personal information.",
};

export default async function PrivacyPolicyPage() {
  const page = await getPolicyPage("privacy-policy");

  if (page) {
    return (
      <PolicyLayout title={page.title}>
        <div dangerouslySetInnerHTML={{ __html: page.body }} />
      </PolicyLayout>
    );
  }

  // Default content
  return (
    <PolicyLayout title="Privacy Policy">
      <h2>Introduction</h2>
      <p>
        At Ready Game Code, we take your privacy seriously. This Privacy Policy
        explains how we collect, use, disclose, and safeguard your information
        when you visit our website and purchase our digital products. Please read
        this policy carefully to understand our practices regarding your personal
        information.
      </p>

      <h2>Information We Collect</h2>
      <p>We collect information you provide directly to us, including:</p>
      <ul>
        <li>
          <strong>Account Information:</strong> Name, email address, username,
          and password when you register for an account.
        </li>
        <li>
          <strong>Payment Information:</strong> Billing details processed
          securely through our payment gateways (Razorpay, PayPal). We do not
          store credit card numbers.
        </li>
        <li>
          <strong>Profile Information:</strong> Phone number, address, and other
          details you choose to add to your profile.
        </li>
        <li>
          <strong>Communication:</strong> Messages you send via our contact form
          or support tickets.
        </li>
      </ul>

      <h2>How We Use Your Information</h2>
      <p>We use the information we collect to:</p>
      <ul>
        <li>Process your orders and deliver digital products</li>
        <li>Communicate with you about your account and orders</li>
        <li>Provide customer support</li>
        <li>Send important notifications about your purchases</li>
        <li>Improve our website and services</li>
        <li>Detect and prevent fraud or abuse</li>
      </ul>

      <h2>Information Sharing</h2>
      <p>
        We do not sell, trade, or rent your personal information to third
        parties. We may share your information with trusted third-party service
        providers who assist us in operating our website (payment processors,
        email services, analytics), provided they agree to keep your information
        confidential.
      </p>

      <h2>Data Security</h2>
      <p>
        We implement appropriate technical and organizational security measures
        to protect your personal information against unauthorized access, loss,
        or destruction. These measures include SSL encryption, secure password
        hashing (bcrypt), and regular security reviews.
      </p>

      <h2>Cookies</h2>
      <p>
        We use cookies to maintain your login session, remember your cart items,
        and analyze website traffic. You can control cookies through your
        browser settings.
      </p>

      <h2>Your Rights</h2>
      <p>You have the right to:</p>
      <ul>
        <li>Access the personal information we hold about you</li>
        <li>Request correction of inaccurate information</li>
        <li>Request deletion of your account</li>
        <li>Opt out of marketing communications</li>
      </ul>

      <h2>Contact Us</h2>
      <p>
        If you have questions about this Privacy Policy, please contact us at{" "}
        <a href="mailto:info@readygamecode.com">info@readygamecode.com</a>.
      </p>
    </PolicyLayout>
  );
}
