import { PolicyLayout } from "@/components/policy/policy-layout";

export const metadata = {
  title: "Terms & Conditions",
  description: "The terms and conditions for using Ready Game Code marketplace.",
};

export default function TermsConditionsPage() {
  return (
    <PolicyLayout title="Terms & Conditions">
      <p className="text-muted-foreground lead text-lg mb-8">
        These policies help explain how we operate, how purchases and support work, and what users should expect when using our marketplace and developer resources.
      </p>

      <div className="space-y-8">
        <section>
          <h2 className="text-xl font-semibold mb-3">Agreement to Terms</h2>
          <p className="text-muted-foreground leading-relaxed">
            By accessing and using Ready Game Code, you accept and agree to be bound by these Terms and Conditions. If you do not agree, please do not use our website. Our products are provided "as is" without warranty of any kind, either expressed or implied.
          </p>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-3">License Types</h2>
          <p className="text-muted-foreground leading-relaxed mb-4">We offer two types of licenses for our digital products:</p>
          <ul className="list-disc pl-6 space-y-2 text-muted-foreground">
            <li>
              <strong className="text-foreground">Personal License:</strong> Allows you to use the source code for personal projects, learning, or a single app publication. Cannot be resold or redistributed.
            </li>
            <li>
              <strong className="text-foreground">Commercial License:</strong> Allows you to use the source code for commercial purposes, including multiple app publications and client projects. Cannot be resold as a standalone product.
            </li>
          </ul>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-3">Additional Services</h2>
          <p className="text-muted-foreground leading-relaxed mb-4">We offer the following add-on services with product purchases:</p>
          <ul className="list-disc pl-6 space-y-2 text-muted-foreground">
            <li>
              <strong className="text-foreground">Reskin Service:</strong> Our team will customize the visual elements of the game (characters, backgrounds, colors) to make it unique.
            </li>
            <li>
              <strong className="text-foreground">Publish Service:</strong> We will publish the game to Google Play Store or Apple App Store on your behalf.
            </li>
            <li>
              <strong className="text-foreground">Store Optimization:</strong> We will optimize your app store listing with SEO-friendly title, description, and keywords.
            </li>
          </ul>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-3">Prohibited Activities</h2>
          <p className="text-muted-foreground leading-relaxed mb-4">You agree not to:</p>
          <ul className="list-disc pl-6 space-y-2 text-muted-foreground">
            <li>Resell, redistribute, or sublicense source codes as standalone products</li>
            <li>Share your purchase codes with others</li>
            <li>Use the products for illegal or harmful activities</li>
            <li>Attempt to reverse engineer or decompile the code</li>
            <li>Remove copyright notices or attribution from the code</li>
          </ul>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-3">Refunds</h2>
          <p className="text-muted-foreground leading-relaxed">
            Refunds are handled according to our Refund Policy. Due to the digital nature of our products, refunds are strictly limited to specific cases where the product is demonstrably broken or fundamentally different from its description.
          </p>
        </section>

        <section>
          <h2 className="text-xl font-semibold mb-3">Changes to Terms</h2>
          <p className="text-muted-foreground leading-relaxed">
            We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting to this page.
          </p>
        </section>
      </div>

      <div className="mt-12 p-6 bg-accent/30 rounded-xl border border-border">
        <h3 className="text-lg font-semibold mb-2">Need help?</h3>
        <p className="text-muted-foreground mb-4">
          If you have questions about licensing, support, refunds, or account access, our team is ready to help.
        </p>
        <a href="/contact" className="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
          Contact us
        </a>
      </div>
    </PolicyLayout>
  );
}
