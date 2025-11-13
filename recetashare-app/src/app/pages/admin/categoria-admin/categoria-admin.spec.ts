import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CategoriaAdmin } from './categoria-admin';

describe('CategoriaAdmin', () => {
  let component: CategoriaAdmin;
  let fixture: ComponentFixture<CategoriaAdmin>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CategoriaAdmin]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CategoriaAdmin);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
