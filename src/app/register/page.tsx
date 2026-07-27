"use client";

import { useState, FormEvent, Suspense, useEffect } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Checkbox } from "@/components/ui/checkbox";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { InputOTP, InputOTPGroup, InputOTPSlot } from "@/components/ui/input-otp";
import { useToast } from "@/hooks/use-toast";
import { Eye, EyeOff, Loader2, User, Store } from "lucide-react";

function RegisterForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const ref = searchParams.get("ref");
  const roleParam = searchParams.get("role") as "buyer" | "seller" | null;
  const { toast } = useToast();

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);
  const [loading, setLoading] = useState(false);
  
  const [step, setStep] = useState<1 | 2>(1);
  const [otp, setOtp] = useState("");
  const [registrationToken, setRegistrationToken] = useState("");
  const [verifying, setVerifying] = useState(false);
  const [timeLeft, setTimeLeft] = useState(15 * 60);
  const [resending, setResending] = useState(false);
  const [form, setForm] = useState({
    firstname: "",
    lastname: "",
    email: "",
    password: "",
    confirmPassword: "",
    agree: false,
    refBy: ref || "",
    role: roleParam === "seller" ? "seller" : "buyer",
  });

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setLoading(true);

    try {
      const res = await fetch("/api/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });

      const data = await res.json();

      if (!res.ok) {
        if (data.details) {
          // Zod validation errors
          const firstError = Object.values(data.details)[0] as string[];
          toast({
            title: "Validation failed",
            description: firstError?.[0] || data.error,
            variant: "destructive",
          });
        } else {
          toast({
            title: "Registration failed",
            description: data.error,
            variant: "destructive",
          });
        }
        return;
      }

      if (data.requiresOTP) {
        setRegistrationToken(data.registrationToken);
        setStep(2);
        setTimeLeft(15 * 60);
        toast({
          title: "Check your email",
          description: "A 6-digit verification code has been sent to your email address.",
        });
      } else {
        toast({
          title: "Welcome to Ready Game Code!",
          description: "Your account has been created successfully.",
        });
        router.push("/dashboard");
        router.refresh();
      }
    } catch (err) {
      toast({
        title: "Network error",
        description: "Could not reach the server. Try again.",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    if (step === 2 && timeLeft > 0) {
      const timerId = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
      return () => clearTimeout(timerId);
    }
  }, [step, timeLeft]);

  const formatTime = (seconds: number) => {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s < 10 ? '0' : ''}${s}`;
  };

  async function handleResendOTP() {
    if (resending) return;
    setResending(true);
    try {
      const res = await fetch("/api/auth/resend-otp", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: form.email, registrationToken }),
      });
      const data = await res.json();
      if (!res.ok) {
        toast({ title: "Failed to resend", description: data.error, variant: "destructive" });
        return;
      }
      setRegistrationToken(data.registrationToken);
      toast({ title: "OTP Sent", description: "A new OTP has been sent to your email." });
      setTimeLeft(15 * 60);
    } catch (err) {
      toast({ title: "Network error", description: "Could not reach the server.", variant: "destructive" });
    } finally {
      setResending(false);
    }
  }

  async function handleVerifyOTP(e: FormEvent) {
    e.preventDefault();
    setVerifying(true);

    try {
      const res = await fetch("/api/auth/verify-otp", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: form.email, otp, registrationToken }),
      });

      const data = await res.json();
      if (!res.ok) {
        toast({ title: "Verification failed", description: data.error, variant: "destructive" });
        return;
      }
      
      toast({ title: "Welcome to Ready Game Code!", description: "Your email has been verified." });
      router.push("/dashboard");
      router.refresh();
    } catch (err) {
      toast({ title: "Network error", description: "Could not reach the server.", variant: "destructive" });
    } finally {
      setVerifying(false);
    }
  }

  return (
    <div className="container mx-auto px-4 py-12">
      <div className="max-w-md mx-auto">
        {step === 1 ? (
        <>
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold">Sign Up to Ready Game Code</h1>
          <p className="text-muted-foreground mt-2">
            Create your account to start buying or selling game source codes.
          </p>
        </div>

        <Tabs
          defaultValue="buyer"
          value={form.role}
          onValueChange={(val) => setForm({ ...form, role: val })}
          className="mb-8"
        >
          <TabsList className="grid w-full grid-cols-2 h-12">
            <TabsTrigger value="buyer" className="text-base gap-2">
              <User className="h-4 w-4" />
              Buyer
            </TabsTrigger>
            <TabsTrigger value="seller" className="text-base gap-2">
              <Store className="h-4 w-4" />
              Seller
            </TabsTrigger>
          </TabsList>
        </Tabs>

        {/* Google OAuth */}
        <Button variant="outline" className="w-full mb-6" size="lg" asChild>
          <Link href="/api/auth/oauth/google">
            <svg className="mr-2 h-5 w-5" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
            </svg>
            Continue with Google
          </Link>
        </Button>

        <div className="relative mb-6">
          <div className="absolute inset-0 flex items-center">
            <span className="w-full border-t border-border" />
          </div>
          <div className="relative flex justify-center text-xs uppercase">
            <span className="bg-background px-2 text-muted-foreground">or</span>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-2 gap-3">
            <div className="space-y-2">
              <Label htmlFor="firstname">First Name *</Label>
              <Input
                id="firstname"
                type="text"
                required
                value={form.firstname}
                onChange={(e) =>
                  setForm({ ...form, firstname: e.target.value })
                }
                placeholder="John"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="lastname">Last Name *</Label>
              <Input
                id="lastname"
                type="text"
                required
                value={form.lastname}
                onChange={(e) =>
                  setForm({ ...form, lastname: e.target.value })
                }
                placeholder="Doe"
              />
            </div>
          </div>

          <div className="space-y-2">
            <Label htmlFor="email">Email *</Label>
            <Input
              id="email"
              type="email"
              required
              value={form.email}
              onChange={(e) => setForm({ ...form, email: e.target.value })}
              placeholder="you@example.com"
              autoComplete="email"
            />
          </div>

          <div className="space-y-2">
            <Label htmlFor="password">Password *</Label>
            <div className="relative">
              <Input
                id="password"
                type={showPassword ? "text" : "password"}
                required
                value={form.password}
                onChange={(e) =>
                  setForm({ ...form, password: e.target.value })
                }
                placeholder="Min 6 characters with letters & numbers"
                autoComplete="new-password"
                className="pr-10"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                aria-label={showPassword ? "Hide password" : "Show password"}
              >
                {showPassword ? (
                  <EyeOff className="h-4 w-4" />
                ) : (
                  <Eye className="h-4 w-4" />
                )}
              </button>
            </div>
            <p className="text-xs text-muted-foreground">
              Min 6 characters, must include upper/lowercase letters and a number.
            </p>
          </div>

          <div className="space-y-2">
            <Label htmlFor="confirmPassword">Confirm Password *</Label>
            <div className="relative">
              <Input
                id="confirmPassword"
                type={showConfirm ? "text" : "password"}
                required
                value={form.confirmPassword}
                onChange={(e) =>
                  setForm({ ...form, confirmPassword: e.target.value })
                }
                placeholder="Re-enter your password"
                autoComplete="new-password"
                className="pr-10"
              />
              <button
                type="button"
                onClick={() => setShowConfirm(!showConfirm)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                aria-label={showConfirm ? "Hide password" : "Show password"}
              >
                {showConfirm ? (
                  <EyeOff className="h-4 w-4" />
                ) : (
                  <Eye className="h-4 w-4" />
                )}
              </button>
            </div>
          </div>

          <div className="flex items-start gap-3 pt-2">
            <Checkbox
              id="agree"
              checked={form.agree}
              onCheckedChange={(checked) =>
                setForm({ ...form, agree: checked === true })
              }
              className="mt-1 flex-shrink-0"
            />
            <div className="text-sm text-muted-foreground leading-relaxed">
              <label htmlFor="agree" className="cursor-pointer hover:text-foreground">
                I agree with the{" "}
              </label>
              <Link href="/privacy-policy" className="text-primary hover:underline font-medium">
                Privacy Policy
              </Link>
              {", "}
              <Link href="/terms-conditions" className="text-primary hover:underline font-medium">
                Terms of Service
              </Link>
              {", and "}
              <Link href="/refund-policy" className="text-primary hover:underline font-medium">
                Refund Policy
              </Link>
              .
            </div>
          </div>

          <Button type="submit" className="w-full" size="lg" disabled={loading}>
            {loading ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                Creating account...
              </>
            ) : (
              "Sign Up"
            )}
          </Button>
        </form>

        <p className="text-center text-sm text-muted-foreground mt-6">
          Already have an account?{" "}
          <Link href="/login" className="text-primary hover:underline font-medium">
            Sign In
          </Link>
        </p>
        </>
        ) : (
          <form onSubmit={handleVerifyOTP} className="space-y-6">
            <div className="text-center mb-6">
              <h2 className="text-xl font-semibold">Enter OTP</h2>
              <p className="text-sm text-muted-foreground mt-2">
                We&apos;ve sent a 6-digit verification code to <strong>{form.email}</strong>.
              </p>
            </div>
            
            <div className="space-y-4 flex flex-col items-center">
              <Label htmlFor="otp">Verification Code</Label>
              <InputOTP 
                maxLength={6} 
                value={otp} 
                onChange={(val) => setOtp(val)} 
                containerClassName="justify-center mt-2"
              >
                <InputOTPGroup className="gap-2">
                  <InputOTPSlot index={0} className="w-12 h-14 text-2xl font-bold rounded-md border" />
                  <InputOTPSlot index={1} className="w-12 h-14 text-2xl font-bold rounded-md border" />
                  <InputOTPSlot index={2} className="w-12 h-14 text-2xl font-bold rounded-md border" />
                  <InputOTPSlot index={3} className="w-12 h-14 text-2xl font-bold rounded-md border" />
                  <InputOTPSlot index={4} className="w-12 h-14 text-2xl font-bold rounded-md border" />
                  <InputOTPSlot index={5} className="w-12 h-14 text-2xl font-bold rounded-md border" />
                </InputOTPGroup>
              </InputOTP>
            </div>

            <div className="text-center text-sm text-muted-foreground mt-4">
              {timeLeft > 0 ? (
                <p>OTP is valid for <span className="font-medium text-primary">{formatTime(timeLeft)}</span></p>
              ) : (
                <p className="text-destructive">OTP has expired</p>
              )}
            </div>

            <Button type="submit" className="w-full" size="lg" disabled={verifying || otp.length !== 6 || timeLeft === 0}>
              {verifying ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Verifying...
                </>
              ) : (
                "Verify OTP"
              )}
            </Button>
            
            <div className="flex flex-col gap-4 text-center">
              <button
                type="button"
                onClick={handleResendOTP}
                disabled={resending || timeLeft > 14 * 60} // prevent spamming within first minute
                className="text-sm font-medium text-primary hover:underline disabled:opacity-50 disabled:hover:no-underline transition-colors"
              >
                {resending ? "Resending..." : "Resend OTP"}
              </button>

              <button 
                type="button" 
                onClick={() => setStep(1)} 
                className="text-sm text-muted-foreground hover:text-foreground transition-colors"
              >
                Change email address
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
}

export default function RegisterPage() {
  return (
    <Suspense fallback={<div className="p-12 text-center">Loading...</div>}>
      <RegisterForm />
    </Suspense>
  );
}
