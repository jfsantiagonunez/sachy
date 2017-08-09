class Addtypetocategory < ActiveRecord::Migration
  def change
    add_column :categories, :ctype, :integer , :default => 0
  end
end
