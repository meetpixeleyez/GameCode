"use client";

import React, { useRef, useEffect } from "react";
import { Bold, Italic, Underline, Heading1, Heading2, List, ListOrdered } from "lucide-react";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";

interface RichTextEditorProps {
  value: string;
  onChange: (value: string) => void;
  className?: string;
}

export function RichTextEditor({ value, onChange, className }: RichTextEditorProps) {
  const editorRef = useRef<HTMLDivElement>(null);
  const isUpdatingRef = useRef(false);

  useEffect(() => {
    if (editorRef.current && !isUpdatingRef.current && editorRef.current.innerHTML !== value) {
      editorRef.current.innerHTML = value;
    }
  }, [value]);

  const handleInput = () => {
    if (editorRef.current) {
      isUpdatingRef.current = true;
      onChange(editorRef.current.innerHTML);
      // reset the ref in a microtask to allow cursor to stay where it is
      queueMicrotask(() => {
        isUpdatingRef.current = false;
      });
    }
  };

  const execCommand = (command: string, value: string | undefined = undefined) => {
    document.execCommand(command, false, value);
    if (editorRef.current) {
      editorRef.current.focus();
      handleInput();
    }
  };

  const ToolbarButton = ({ onClick, children, title }: { onClick: () => void, children: React.ReactNode, title: string }) => (
    <Button
      type="button"
      variant="ghost"
      size="sm"
      className="h-8 w-8 p-0"
      onClick={onClick}
      title={title}
    >
      {children}
    </Button>
  );

  return (
    <div className={cn("border border-input rounded-md overflow-hidden flex flex-col focus-within:ring-1 focus-within:ring-ring", className)}>
      <div className="flex items-center flex-wrap gap-1 border-b bg-muted/40 p-1">
        <ToolbarButton onClick={() => execCommand('bold')} title="Bold">
          <Bold className="h-4 w-4" />
        </ToolbarButton>
        <ToolbarButton onClick={() => execCommand('italic')} title="Italic">
          <Italic className="h-4 w-4" />
        </ToolbarButton>
        <ToolbarButton onClick={() => execCommand('underline')} title="Underline">
          <Underline className="h-4 w-4" />
        </ToolbarButton>
        
        <div className="w-px h-4 bg-border mx-1" />
        
        <ToolbarButton onClick={() => execCommand('formatBlock', 'H1')} title="Heading 1">
          <Heading1 className="h-4 w-4" />
        </ToolbarButton>
        <ToolbarButton onClick={() => execCommand('formatBlock', 'H2')} title="Heading 2">
          <Heading2 className="h-4 w-4" />
        </ToolbarButton>
        
        <div className="w-px h-4 bg-border mx-1" />
        
        <ToolbarButton onClick={() => execCommand('insertUnorderedList')} title="Bullet List">
          <List className="h-4 w-4" />
        </ToolbarButton>
        <ToolbarButton onClick={() => execCommand('insertOrderedList')} title="Numbered List">
          <ListOrdered className="h-4 w-4" />
        </ToolbarButton>
      </div>
      
      <div
        ref={editorRef}
        contentEditable
        onInput={handleInput}
        className="min-h-[200px] p-3 outline-none prose prose-sm max-w-none dark:prose-invert focus:outline-none"
        style={{ whiteSpace: "pre-wrap" }}
      />
    </div>
  );
}
