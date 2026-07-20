import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { setAuthCookies } from "@/lib/auth";
import bcrypt from "bcryptjs";
import { z } from "zod";

const registerSchema = z
  .object({
    firstname: z.string().min(1, "First name is required"),
    lastname: z.string().min(1, "Last name is required"),
    email: z.string().email("Invalid email").toLowerCase(),
    password: z
      .string()
      .min(6, "Password must be at least 6 characters")
      .regex(/[A-Z]/, "Must contain an uppercase letter")
      .regex(/[a-z]/, "Must contain a lowercase letter")
      .regex(/[0-9]/, "Must contain a number"),
    confirmPassword: z.string(),
    agree: z
      .boolean()
      .refine((v) => v === true, "You must agree to the terms"),
  })
  .refine((data) => data.password === data.confirmPassword, {
    message: "Passwords do not match",
    path: ["confirmPassword"],
  });

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const parsed = registerSchema.safeParse(body);
    if (!parsed.success) {
      return NextResponse.json(
        {
          error: "Validation failed",
          details: parsed.error.flatten().fieldErrors,
        },
        { status: 400 }
      );
    }

    const { firstname, lastname, email, password } = parsed.data;

    // Check if email already exists
    const existing = await db.user.findUnique({ where: { email } });
    if (existing) {
      return NextResponse.json(
        { error: "Email already registered" },
        { status: 409 }
      );
    }

    // Hash password with $2b$ at cost 12 (modern Node bcrypt)
    const hashedPassword = await bcrypt.hash(password, 12);

    // Generate username from email if not provided (firstname + lastname initial)
    const baseUsername = `${firstname.toLowerCase()}${lastname.charAt(0).toLowerCase()}`;
    let username = baseUsername;
    let suffix = 1;
    while (await db.user.findUnique({ where: { username } })) {
      username = `${baseUsername}${suffix}`;
      suffix++;
    }

    const user = await db.user.create({
      data: {
        email,
        firstname,
        lastname,
        username,
        password: hashedPassword,
        passwordAlgo: "bcrypt-2b",
        // Match Laravel behavior: if email verification is OFF, auto-verify
        ev: 1,
        sv: 0,
        kv: 0,
        ts: 0,
        tv: 1,
        status: 1,
        profileComplete: 0, // user must complete profile on first login
      },
    });

    // Issue JWT tokens
    await setAuthCookies({
      sub: user.id,
      email: user.email,
      role: "user",
      username: user.username,
    });

    return NextResponse.json(
      {
        success: true,
        user: {
          id: user.id,
          email: user.email,
          username: user.username,
          firstname: user.firstname,
          lastname: user.lastname,
        },
      },
      { status: 201 }
    );
  } catch (error) {
    console.error("Registration error:", error);
    return NextResponse.json(
      { error: "Internal server error" },
      { status: 500 }
    );
  }
}
