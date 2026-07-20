import { getPolicyPage, PolicyLayout } from "@/components/policy/policy-layout";

export const metadata = {
  title: "Terms & Conditions",
  description: "The terms and conditions for using Ready Game Code marketplace.",
};

export default async function TermsConditionsPage() {
  const page = await getPolicyPage("terms-conditions");

  if (page) {
    return (
      <PolicyLayout title={page.title}>
        <div dangerouslySetInnerHTML={{ __html: page.body }} />
      </PolicyLayout>
    );
  }

  return (
    <PolicyLayout title="Terms & Conditions">
      <h2>Agreement to Terms</h2>
      <p>
        By accessing and using Ready Game Code, you accept and agree to be bound
        by these Terms and Conditions. If you do not agree, please do not use our
        website.
      </p>

      <h2>License Types</h2>
      <p>We offer two types of licenses for our digital products:</p>
      <ul>
        <li>
          <strong>Personal License:</strong> Allows you to use the source code
          for personal projects, learning, or a single app publication. Cannot
          be resold or redistributed.
        </li>
        <li>
          <strong>Commercial License:</strong> Allows you to use the source code
          for commercial purposes, including multiple app publications and
          client projects. Cannot be resold as a standalone product.
        </li>
      </ul>
      <p>
        An optional <strong>12-month Extended License</strong> can be purchased
        for extended support and updates.
      </p>

      <h2>Additional Services</h2>
      <p>We offer the following add-on services with product purchases:</p>
      <ul>
        <li>
          <strong>Reskin Service:</strong> Our team will customize the visual
          elements of the game (characters, backgrounds, colors) to make it
          unique.
        </li>
        <li>
          <strong>Publish Service:</strong> We will publish the game to Google
          Play Store or Apple App Store on your behalf.
        </li>
        <li>
          <strong>Store Optimization:</strong> We will optimize your app store
          listing with SEO-friendly title, description, and keywords.
        </li>
      </ul>

      <h2>Prohibited Activities</h2>
      <p>You agree not to:</p>
      <ul>
        <li>Resell, redistribute, or sublicense source codes as standalone products</li>
        <li>Share your purchase codes with others</li>
        <li>Use the products for illegal or harmful activities</li>
        <li>Attempt to reverse engineer or decompile the code</li>
        <li>Remove copyright notices or attribution from the code</li>
      </ul>

      <h2>Seller Agreement</h2>
      <p>
        If you become an author/seller on our platform, you agree that: (1) your
        products are original or properly licensed, (2) you will provide support
        to buyers for 3 months, (3) you will deliver future updates, and (4) a
        seller fee (percentage based on your author level) will be deducted from
        your earnings.
      </p>

      <h2>Refunds</h2>
      <p>
        Refunds are handled according to our Refund Policy. Due to the digital
        nature of our products, refunds are limited to specific cases.
      </p>

      <h2>Limitation of Liability</h2>
      <p>
        Ready Game Code is not liable for any indirect, incidental, or
        consequential damages arising from the use of our products. Our total
        liability shall not exceed the amount you paid for the product.
      </p>

      <h2>Changes to Terms</h2>
      <p>
        We reserve the right to modify these terms at any time. Changes will be
        effective immediately upon posting to this page.
      </p>

      <h2>Contact</h2>
      <p>
        For questions about these Terms, contact{" "}
        <a href="mailto:info@readygamecode.com">info@readygamecode.com</a>.
      </p>
    </PolicyLayout>
  );
}
