import { getPolicyPage, PolicyLayout } from "@/components/policy/policy-layout";

export const metadata = {
  title: "Refund Policy",
  description: "Refund policy for digital products purchased on Ready Game Code.",
};

export default async function RefundPolicyPage() {
  const page = await getPolicyPage("refund-policy");

  if (page) {
    return (
      <PolicyLayout title={page.title}>
        <div dangerouslySetInnerHTML={{ __html: page.body }} />
      </PolicyLayout>
    );
  }

  return (
    <PolicyLayout title="Refund Policy">
      <h2>Digital Product Refunds</h2>
      <p>
        Due to the nature of digital products, all sales are generally
        considered final. However, we understand that issues can arise. This
        policy outlines the cases where refunds are available.
      </p>

      <h2>Refund Eligibility</h2>
      <p>You may be eligible for a refund if:</p>
      <ul>
        <li>
          The product does not work as described in the product documentation
        </li>
        <li>
          The product has critical bugs that prevent basic functionality and the
          author cannot fix them within 7 days
        </li>
        <li>
          You have NOT downloaded the source code yet (verified via our download
          logs)
        </li>
        <li>The product was misrepresented in the listing</li>
        <li>You purchased by mistake and contact us within 24 hours</li>
      </ul>

      <h2>Non-Refundable Cases</h2>
      <p>Refunds will NOT be granted if:</p>
      <ul>
        <li>You have already downloaded the source code</li>
        <li>The product works as described but you changed your mind</li>
        <li>You lack the technical skills to use the product</li>
        <li>The product was purchased more than 7 days ago</li>
        <li>
          The issue is related to third-party services (AdMob, Firebase, etc.)
          not covered by the product
        </li>
        <li>You purchased additional services (Reskin, Publish, Store Optimization) that have been started</li>
      </ul>

      <h2>How to Request a Refund</h2>
      <p>
        To request a refund, please{" "}
        <a href="/contact">contact our support team</a> with:
      </p>
      <ul>
        <li>Your order number (found in your dashboard)</li>
        <li>The reason for your refund request</li>
        <li>Screenshots or videos showing the issue (if applicable)</li>
      </ul>
      <p>
        Our team will review your request within 2-3 business days. If approved,
        refunds are processed back to the original payment method within 5-7
        business days.
      </p>

      <h2>Refund Method</h2>
      <p>
        Refunds are processed through the original payment method:
      </p>
      <ul>
        <li>
          <strong>Razorpay:</strong> Refunded to your original payment method
          (UPI, card, net banking)
        </li>
        <li>
          <strong>PayPal:</strong> Refunded to your PayPal account
        </li>
        <li>
          <strong>Manual UPI:</strong> Refunded via UPI transfer to your account
        </li>
        <li>
          <strong>Wallet Balance:</strong> Credited back to your Ready Game Code
          wallet
        </li>
      </ul>

      <h2>Seller-Issued Refunds</h2>
      <p>
        If you have an issue with a specific product, you can also request a
        refund directly from the seller. Sellers have the option to accept or
        reject refund requests. If rejected, you can escalate to Ready Game Code
        support for mediation.
      </p>

      <h2>Contact</h2>
      <p>
        For refund questions, contact{" "}
        <a href="mailto:info@readygamecode.com">info@readygamecode.com</a> with
        your order details.
      </p>
    </PolicyLayout>
  );
}
